/** Any error carrying an HTTP response (AxiosError, precognition HttpResponseError, ...) */
type ResponseError = { response?: { data?: unknown } };

export default class ToastError extends Error {
    constructor(msg: string, axiosError: ResponseError | undefined = undefined) {
        super(msg);

        if (axiosError != undefined) {
            console.error(JSON.stringify(axiosError.response?.data));
        }

        Object.setPrototypeOf(this, ToastError.prototype);
    }
}
