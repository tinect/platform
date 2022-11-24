import template from './sw-extension-rating-modal.html.twig';
import './sw-extension-rating-modal.scss';

/**
 * @private
 */
export default {
    template,

    methods: {
        emitClose() {
            this.$emit('modal-close');
        },
    },
};
