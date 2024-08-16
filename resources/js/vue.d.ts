declare module "*.vue" {
    import type {DefineComponent} from "vue";

    const component: DefineComponent<Object, Object, any>;
    export default component;
}
