import template from './sw-url-field.html.twig';
import './sw-url-field.scss';

const { ShopwareError } = Shopware.Classes;

const URL_REGEX = {
    TRAILING_SLASH: /\/+$/,
};

const domainPlaceholderId = '124c71d524604ccbad6042edce3ac799';
// TODO: WHY ISN'T THERE ANY GLOBAL VARIABLE FOR THIS? THIS IS DEFINED IN TS, JS AND PHP MULTIPLE TIMES

/**
 * @public
 * @description Url field component which supports a switch for https and http.
 * @status ready
 * @example-type dynamic
 * @component-example
 * <sw-field type="url" label="Name" placeholder="Placeholder"
 * switchLabel="My shop uses https"></sw-field>
 */
Shopware.Component.extend('sw-url-field', 'sw-text-field', {
    template,
    inheritAttrs: false,

    props: {
        error: {
            type: Object,
            required: false,
            default: null,
        },
        omitUrlHash: {
            type: Boolean,
            default: false,
        },
        omitUrlSearch: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            currentValue: this.value || '',
            errorUrl: null,
            currentDebounce: null,
            prefixTypes: [
                {
                    name: '',
                    prefix: '',
                },
                {
                    name: 'http',
                    prefix: 'http://',
                },
                {
                    name: 'https',
                    prefix: 'https://',
                },
                {
                    name: 'mailto',
                    prefix: 'mailto:',
                },
                {
                    name: 'tel',
                    prefix: 'tel:',
                },
                {
                    name: 'product',
                    prefix: `${domainPlaceholderId}/detail/`,
                },
                {
                    name: 'category',
                    prefix: `${domainPlaceholderId}/navigation/`,
                },
            ],
            activePrefixType: '',
            urlPrefix: '',
        };
    },

    computed: {
        url() {
            let trimmedValue = this.currentValue.trim();
            if (trimmedValue === '') {
                return '';
            }

            if ((this.activePrefixType === 'product' || this.activePrefixType === 'category')
                && !trimmedValue.endsWith('#')) {
                trimmedValue += '#';
            }

            return `${this.urlPrefix}${trimmedValue}`;
        },
    },

    watch: {
        value() {
            this.checkInput(this.value || '');
        },

        currentValue() {
            this.checkInput(this.currentValue || '');
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.checkInput(this.currentValue);
        },

        onBlur(event) {
            this.checkInput(event.target.value);
        },

        checkInput(inputValue) {
            this.errorUrl = null;

            if (!inputValue.length) {
                this.handleEmptyUrl();

                return;
            }

            const validated = this.validateCurrentValue(inputValue);

            if (!validated) {
                this.setInvalidUrlError();
                return;
            }

            this.currentValue = validated;
            this.$emit('input', this.url);
        },

        handleEmptyUrl() {
            this.currentValue = '';
            this.$emit('input', '');
        },

        validateCurrentValue(value) {
            const url = this.getURL(value);

            if (url instanceof URL) {
                if (this.omitUrlSearch) {
                    url.search = '';
                }

                if (this.omitUrlHash) {
                    url.hash = '';
                }

                // when a hash or search query is provided we want to allow trailing slash, eg a vue route `admin#/`
                const removeTrailingSlash = url.hash === '' && url.search === '' ? URL_REGEX.TRAILING_SLASH : '';

                // build URL via native URL.toString() function instead by hand @see NEXT-15747
                value = url.toString()
                    .replace(removeTrailingSlash, '')
                    .replace(url.host, this.$options.filters.unicodeUri(url.host));
            }

            this.prefixTypes.forEach((prefix) => {
                if (value.startsWith(prefix.prefix)) {
                    value = value.replace(prefix.prefix, '');
                }
            });

            value = value.replace(/#+$/g, '');

            return value;
        },

        getURL(value) {
            try {
                let url = `${this.urlPrefix}${value}`;

                this.prefixTypes.forEach((prefix) => {
                    if (prefix.prefix !== '' && value.startsWith(prefix.prefix)) {
                        this.urlPrefix = prefix.prefix;
                        this.activePrefixType = prefix.name;

                        if (prefix.name === 'product' || prefix.name === 'category') {
                            const slicedLink = value.split('/');
                            if (value.startsWith(domainPlaceholderId)) {
                                this.currentValue = slicedLink[2].substr(0, 32);
                            }
                        }

                        url = value;
                    }
                });

                return new URL(url);
            } catch {
                return null;
            }
        },

        updateActivatedPrefixType() {
            this.currentValue = ''; // WE OVERRIDE HERE BECAUSE NO VALUE IS COMPATIBLE WITH OTHER PREFIXES
            this.prefixTypes.forEach((prefix) => {
                if (prefix.prefix === this.urlPrefix) {
                    this.activePrefixType = prefix.name;
                }
            });
        },

        setInvalidUrlError() {
            this.errorUrl = new ShopwareError({
                code: 'INVALID_URL',
            });
        },
    },
});
