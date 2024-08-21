import axios, { AxiosError } from "axios";

export default class ToastError extends Error {
    constructor(msg: string, axiosError: AxiosError | undefined = undefined) {
        super(msg);

        if (axiosError != undefined) {
            console.error(JSON.stringify(axiosError.response?.data));
        }

        Object.setPrototypeOf(this, ToastError.prototype);
    }
}
