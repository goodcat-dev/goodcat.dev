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

Alpine.data('themeToggle', () => ({
    theme: document.documentElement.dataset.theme,

    init() {
        matchMedia('(prefers-color-scheme: light)').addEventListener('change', (event) => {
            if (! localStorage.getItem('theme')) {
                this.apply(event.matches ? 'light' : 'dark');
            }
        });

        addEventListener('pagereveal', () => (this.theme = document.documentElement.dataset.theme));
    },

    toggle() {
        this.apply(this.theme === 'light' ? 'dark' : 'light');
        localStorage.setItem('theme', this.theme);
    },

    apply(theme) {
        this.theme = theme;
        document.documentElement.dataset.theme = theme;
    },
}));

Alpine.data('docsSearch', (config) => ({
    query: '',
    hits: [],
    active: 0,
    loading: false,
    controller: null,
    isMac: /Mac|iPhone|iPad/.test(navigator.platform),
    enabled: config.enabled,

    open() {
        this.$refs.dialog.showModal();
        this.$nextTick(() => this.$refs.input.select());
    },

    close() {
        this.$refs.dialog.close();
    },

    shortcut(event) {
        if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
            event.preventDefault();
            this.$refs.dialog.open ? this.close() : this.open();
        }
    },

    async search() {
        const query = this.query.trim();

        this.controller?.abort();

        if (! this.enabled || query.length < 2) {
            this.hits = [];

            return;
        }

        this.controller = new AbortController();
        this.loading = true;

        try {
            const response = await fetch(`https://${config.appId}-dsn.algolia.net/1/indexes/${config.index}/query`, {
                method: 'POST',
                signal: this.controller.signal,
                headers: {
                    'X-Algolia-Application-Id': config.appId,
                    'X-Algolia-API-Key': config.searchKey,
                },
                body: JSON.stringify({
                    query,
                    filters: config.filters,
                    queryLanguages: [config.lang],
                    ignorePlurals: [config.lang],
                    removeStopWords: [config.lang],
                    hitsPerPage: 8,
                    highlightPreTag: '__mark__',
                    highlightPostTag: '__/mark__',
                }),
            });
            const result = await response.json();

            this.hits = (result.hits ?? []).map((hit) => ({
                url: hit.url,
                title: this.highlight(
                    hit._highlightResult.hierarchy?.lvl3?.value ??
                        hit._highlightResult.hierarchy?.lvl2?.value ??
                        hit._highlightResult.hierarchy?.lvl1?.value ??
                        '',
                ),
                path: [hit.hierarchy.lvl1, hit.hierarchy.lvl2, hit.hierarchy.lvl3].filter(Boolean).slice(0, -1).join(' › '),
                snippet: this.highlight(hit._snippetResult?.content?.value ?? ''),
            }));
            this.active = 0;
        } catch (error) {
            if (error.name !== 'AbortError') {
                this.hits = [];
            }
        } finally {
            this.loading = false;
        }
    },

    highlight(value) {
        const escaped = value.replace(/[&<>"']/g, (character) => `&#${character.charCodeAt(0)};`);

        return escaped.replaceAll('__mark__', '<mark>').replaceAll('__/mark__', '</mark>');
    },

    move(step) {
        if (this.hits.length > 0) {
            this.active = (this.active + step + this.hits.length) % this.hits.length;
            this.$refs.results.children[this.active + 1]?.scrollIntoView({ block: 'nearest' });
        }
    },

    go() {
        const hit = this.hits[this.active];

        if (hit) {
            this.close();
            window.location.href = hit.url;
        }
    },
}));

window.Alpine = Alpine;

Alpine.start();
