<?php

namespace App\Services\Strategy;

use App\Entities\SheetEntity;
use App\Entities\StrategyContext;
use App\Entities\StrategyInput;
use App\Entities\StrategyResult;
use App\Entities\StrategySheet;
use App\Exceptions\StrategyException;
use Closure;
use LuaSandbox;
use LuaSandboxError;
use LuaSandboxSyntaxError;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;

class StrategyEngine
{
    private const string CHUNK_NAME = "=strategy";

    /** @var array<string, Closure> */
    public array $library { get => array_column($this->contract, "callback", "name"); }

    /** @var array<string, string> */
    public array $api { get => [...$this->contextApi, ...array_column($this->contract, "doc", "label")]; }

    /** @var array<string, string> */
    private array $contextApi { get => $this->propertyDocs(StrategyContext::class, "ctx"); }

    /** @var list<array{name: string, label: string, callback: Closure, doc: string}> */
    private array $contract { get => $this->contract(); }

    /**
     * Run a strategy. User and targets must be built with SheetEntity::buildFromModel().
     *
     * @param list<SheetEntity> $targets
     * @throws StrategyException
     */
    public function run(string $code, SheetEntity $user, array $targets = [], StrategyInput $input = new StrategyInput()): StrategyResult
    {
        $run = new StrategyRun($user, $targets);
        $sandbox = $this->makeSandbox();
        $sandbox->registerLibrary("rpg", array_map(
            fn (Closure $function): Closure => fn (mixed ...$arguments): array => $function($run, ...$arguments),
            $this->library,
        ));

        try {
            // @phpstan-ignore argument.type (the stub types ...$args as string, but any PHP value is converted to Lua)
            $sandbox->loadString($code, self::CHUNK_NAME)->call($this->luaValue(new StrategyContext(
                user: StrategySheet::fromSheet($user),
                targets: array_map(StrategySheet::fromSheet(...), $targets),
                input: $input,
            )));
        } catch (LuaSandboxError $exception) {
            throw new StrategyException($exception->getMessage(), previous: $exception);
        }

        $run->applyChanges();

        return $run->result;
    }

    /**
     * Compile without running, returning the syntax error message if there is one.
     */
    public function syntaxError(string $code): ?string
    {
        try {
            $this->makeSandbox()->loadString($code, self::CHUNK_NAME);
        } catch (LuaSandboxSyntaxError $exception) {
            return $exception->getMessage();
        }

        return null;
    }

    private function makeSandbox(): LuaSandbox
    {
        $sandbox = new LuaSandbox();
        $sandbox->setMemoryLimit(config("strategy.memory_limit"));
        $sandbox->setCPULimit(config("strategy.cpu_limit"));

        return $sandbox;
    }

    /**
     * The docblock text of a method or property without its tags, one line per docblock line.
     */
    private function docSummary(ReflectionMethod|ReflectionProperty $member): string
    {
        $docBlock = (string) preg_replace("#^/\*\*|\*/$#", "", trim($member->getDocComment() ?: ""));
        $lines = array_map(fn (string $line): string => trim(ltrim(trim($line), "*")), explode("\n", $docBlock));

        return implode(PHP_EOL, array_filter($lines, fn (string $line): bool => $line !== "" && !str_starts_with($line, "@")));
    }

    /**
     * Labels and docblock summaries of a context DTO's public properties, recursing into properties typed as another DTO.
     *
     * @param class-string $class
     * @return array<string, string>
     */
    private function propertyDocs(string $class, string $prefix): array
    {
        $docs = [];
        foreach (new ReflectionClass($class)->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $label = "{$prefix}.{$property->getName()}";
            $docs[$label] = $this->docSummary($property);

            $type = $property->getType();
            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                /** @var class-string $propertyClass */
                $propertyClass = $type->getName();
                $docs = [...$docs, ...$this->propertyDocs($propertyClass, $label)];
            }
        }

        return $docs;
    }

    /**
     * Convert a context DTO into what LuaSandbox can pass to Lua: objects become tables and lists become 1-based.
     */
    private function luaValue(mixed $value): mixed
    {
        if (is_object($value)) {
            $value = get_object_vars($value);
        } elseif (!is_array($value)) {
            return $value;
        } elseif ($value !== [] && array_is_list($value)) {
            $value = array_combine(range(1, count($value)), $value);
        }

        return array_map($this->luaValue(...), $value);
    }

    private function contract(): array
    {
        return collect(new ReflectionClass(StrategyLibrary::class)->getMethods(ReflectionMethod::IS_STATIC))
            ->filter(fn (ReflectionMethod $method): bool => $method->isPublic())
            ->map(fn (ReflectionMethod $method): array => [
                "name" => $method->getName(),
                "label" => "rpg." . $method->getName(),
                "callback" => $method->getClosure(),
                "doc" => $this->docSummary($method),
            ])
            ->values()
            ->all();
    }
}
