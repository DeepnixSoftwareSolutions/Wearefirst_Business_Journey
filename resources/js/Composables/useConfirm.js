import { reactive } from 'vue';

const state = reactive({
    isOpen: false,
    title: '',
    message: '',
    confirmText: 'Confirm',
    cancelText: 'Cancel',
    confirmButtonTheme: 'primary', // 'primary', 'danger', 'success'
    onConfirm: null,
    onCancel: null,
});

export function useConfirm() {
    const confirmAction = (options) => {
        state.title = options.title || 'Please Confirm';
        state.message = options.message || 'Are you sure you want to proceed?';
        state.confirmText = options.confirmText || 'Confirm';
        state.cancelText = options.cancelText || 'Cancel';
        state.confirmButtonTheme = options.confirmButtonTheme || 'primary';
        
        state.isOpen = true;

        return new Promise((resolve) => {
            state.onConfirm = () => {
                state.isOpen = false;
                resolve(true);
            };
            state.onCancel = () => {
                state.isOpen = false;
                resolve(false);
            };
        });
    };

    return { state, confirmAction };
}