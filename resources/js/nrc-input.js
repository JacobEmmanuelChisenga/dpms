/**
 * Live formatter for Zambian NRC: NNNNNN/NN/N (9 digits).
 */
export function nrcInput(initial = '') {
    return {
        display: formatNrcDisplay(digitsOnly(initial)),

        onInput(event) {
            const digits = (event.target.value || '').replace(/\D/g, '').slice(0, 9);
            this.display = formatNrcDisplay(digits);
            event.target.value = this.display;
        },

        onPaste(event) {
            event.preventDefault();
            const pasted = (event.clipboardData?.getData('text') || '').replace(/\D/g, '').slice(0, 9);
            this.display = formatNrcDisplay(pasted);
            event.target.value = this.display;
        },
    };
}

export function formatNrcDisplay(digits) {
    if (digits.length <= 6) {
        return digits;
    }

    if (digits.length <= 8) {
        return `${digits.slice(0, 6)}/${digits.slice(6)}`;
    }

    return `${digits.slice(0, 6)}/${digits.slice(6, 8)}/${digits.slice(8, 9)}`;
}

export function digitsOnly(value) {
    return (value || '').replace(/\D/g, '');
}
