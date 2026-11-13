/**
 * Lua code editor for the Filament panel (App\Filament\Components\LuaCodeEditor).
 *
 * Adapted from Filament's code-editor Alpine component
 * (vendor/filament/forms/resources/js/components/code-editor.js), whose language list is closed,
 * with Lua support from @fazelstudio/codemirror-lang-lua. Re-check that file when upgrading Filament.
 *
 * Build: `npm run build:filament`, then `php artisan filament:assets` to publish it.
 */
import { EditorState, Compartment } from '@codemirror/state'
import { EditorView, basicSetup } from 'codemirror'
import { indentWithTab } from '@codemirror/commands'
import { oneDark } from '@codemirror/theme-one-dark'
import { keymap } from '@codemirror/view'
import { autocompletion, completeFromList, ifNotIn } from '@codemirror/autocomplete'
import { lua, luaCompletionList } from '@fazelstudio/codemirror-lang-lua'
import { observeFieldAccessibility } from '../../../vendor/filament/forms/resources/js/utilities/field-accessibility.js'

/**
 * Globals LuaSandbox exposes (Lua 5.1 without io, require, package, coroutine, debug and most of os).
 * The package's stdlib completions are filtered against this, so the editor never suggests
 * functions that would fail inside the sandbox.
 */
const SANDBOX_GLOBALS = new Set(
    (
        '_G _VERSION assert error getmetatable ipairs next pairs pcall rawequal rawget rawset select setmetatable ' +
        'tonumber tostring type unpack xpcall os.clock os.date os.difftime os.time ' +
        'math.abs math.acos math.asin math.atan math.atan2 math.ceil math.cos math.cosh math.deg math.exp math.floor ' +
        'math.fmod math.frexp math.huge math.ldexp math.log math.log10 math.max math.min math.modf math.pi math.pow ' +
        'math.rad math.random math.sin math.sinh math.sqrt math.tan math.tanh ' +
        'string.byte string.char string.find string.format string.gmatch string.gsub string.len string.lower ' +
        'string.match string.rep string.reverse string.sub string.upper ' +
        'table.concat table.insert table.maxn table.remove table.sort'
    ).split(' '),
)

/** Lua 5.2+ syntax the package offers but Lua 5.1 rejects. */
const UNSUPPORTED_SYNTAX = new Set(['goto', '::'])

/**
 * Completion source: the strategy API, plus the package's keywords, snippets and the stdlib entries
 * that exist inside the sandbox. Exported for headless checks; Async Alpine only uses the default export.
 *
 * @param {Record<string, string>} api Strategy API from StrategyEngine::$api (label => detail)
 */
export function sandboxCompletion(api) {
    const builtins = luaCompletionList.filter(
        (completion) =>
            !UNSUPPORTED_SYNTAX.has(completion.label) &&
            (completion.section === 'Keyword' || completion.type === 'keyword' || SANDBOX_GLOBALS.has(completion.label)),
    )

    const strategyApi = Object.entries(api).map(([label, detail]) => ({
        label,
        detail,
        type: label.startsWith('rpg.') ? 'function' : 'variable',
        section: 'Estratégia',
        boost: 1,
    }))

    return ifNotIn(
        ['String', 'LongString', 'LineComment', 'BlockComment'],
        completeFromList([...strategyApi, ...builtins]),
    )
}

export default function luaCodeEditorFormComponent({
    api,
    canWrap,
    isDisabled,
    isLive,
    isLiveDebounced,
    isLiveOnBlur,
    label,
    liveDebounce,
    state,
}) {
    return {
        editor: null,
        themeCompartment: new Compartment(),
        accessibilityCompartment: new Compartment(),
        accessibilityObserver: null,
        isDocChanged: false,
        state,

        init() {
            const debouncedCommit = Alpine.debounce(() => {
                if (this.editor) {
                    this.$wire.commit()
                }
            }, liveDebounce ?? 300)

            this.editor = new EditorView({
                parent: this.$refs.editor,
                state: EditorState.create({
                    doc: this.state ?? '',
                    extensions: [
                        basicSetup,
                        this.accessibilityCompartment.of([]),
                        keymap.of([indentWithTab]),
                        ...(label
                            ? [
                                  EditorView.contentAttributes.of({
                                      'aria-label': label,
                                  }),
                              ]
                            : []),
                        ...(canWrap ? [EditorView.lineWrapping] : []),
                        EditorState.readOnly.of(isDisabled),
                        EditorView.editable.of(!isDisabled),
                        EditorView.updateListener.of((viewUpdate) => {
                            if (!viewUpdate.docChanged) {
                                return
                            }
                            this.isDocChanged = true
                            this.state = viewUpdate.state.doc.toString()
                            if (!isLiveOnBlur && (isLive || isLiveDebounced)) {
                                debouncedCommit()
                            }
                        }),
                        EditorView.domEventHandlers({
                            blur: () => {
                                if (isLiveOnBlur && this.isDocChanged) {
                                    this.$wire.$commit()
                                }
                            },
                        }),
                        lua(),
                        // The package always registers its full stdlib completions as language data
                        // (enableCompletion: false doesn't remove them), so override every source instead.
                        autocompletion({ override: [sandboxCompletion(api ?? {})] }),
                        this.themeCompartment.of(this.getThemeExtensions()),
                    ],
                }),
            })

            this.accessibilityObserver = observeFieldAccessibility(this.$el, (attributes) => {
                this.editor.dispatch({
                    effects: this.accessibilityCompartment.reconfigure(
                        EditorView.contentAttributes.of(
                            Object.fromEntries(Object.entries(attributes).filter(([, value]) => value !== null)),
                        ),
                    ),
                })
            })

            this.$watch('state', () => {
                if (this.state === undefined) {
                    return
                }

                if (this.editor.state.doc.toString() === (this.state ?? '')) {
                    return
                }

                this.editor.dispatch({
                    changes: {
                        from: 0,
                        to: this.editor.state.doc.length,
                        insert: this.state ?? '',
                    },
                })
            })

            this.themeObserver = new MutationObserver(() => {
                this.editor.dispatch({
                    effects: this.themeCompartment.reconfigure(this.getThemeExtensions()),
                })
            })

            this.themeObserver.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class'],
            })
        },

        isDarkMode() {
            return document.documentElement.classList.contains('dark')
        },

        getThemeExtensions() {
            return this.isDarkMode() ? [oneDark] : []
        },

        destroy() {
            this.accessibilityObserver?.disconnect()

            if (this.themeObserver) {
                this.themeObserver.disconnect()
                this.themeObserver = null
            }

            if (this.editor) {
                this.editor.destroy()
                this.editor = null
            }
        },
    }
}
