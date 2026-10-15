import Alpine from 'alpinejs';

Alpine.data('tabs', (initial) => ({
    active: initial,

    select(tab) {
        this.active = tab;
    },

    isActive(tab) {
        return this.active === tab;
    },
}));

Alpine.data('copyCode', () => ({
    copied: false,

    async copy(button) {
        const code = [...button.closest('[data-code]').querySelectorAll('pre')].find((pre) => pre.offsetParent !== null);

        await navigator.clipboard.writeText(code.innerText);

        this.copied = true;
        setTimeout(() => (this.copied = false), 2000);
    },
}));

Alpine.data('disclosure', () => ({
    open: false,

    toggle() {
        this.open = !this.open;
    },
}));

Alpine.data('feedback', () => ({
    voted: false,

    vote() {
        this.voted = true;
    },
}));

window.Alpine = Alpine;

Alpine.start();
