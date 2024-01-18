import Plugin from 'src/plugin-system/plugin.class'

import PluginManager from 'src/plugin-system/plugin.manager';
import FormValidationPlugin from 'src/plugin/forms/form-validation.plugin';
import Iterator from 'src/helper/iterator.helper';

export default class OrderPickingPlugin extends Plugin {
    static options = {
        pickingLists: [],
        visiblePickingLists: []
    };

    init() {
        this.registerEventListeners();

        const _self = this;

        this.el.querySelector('button#order')
            .addEventListener('click', function (evt) {});

        this.el.querySelector('form#shipping-details')
            .addEventListener('submit', function (evt) {
                evt.preventDefault();
                evt.stopPropagation();

                if (_self.el.querySelectorAll('input:invalid').length === 0) {
                    _self.order();
                }
            });
    }

    registerEventListeners() {
        const _self = this;

        this.el.querySelector('[data-picking-list-select-all]').addEventListener(
            'click',
            function (evt) {
                _self.setAllProductsChecked(evt.target.checked);

                _self.el
                    .querySelectorAll('[data-picking-list] input[type=checkbox]')
                    .forEach(function (pickingListCheckbox) {
                        pickingListCheckbox.checked = evt.target.checked;
                    });
            }
        );

        const pickingLists = this.el.querySelectorAll('[data-picking-list]');

        pickingLists.forEach(function (pickingListRow) {
            const pickingListNumber = pickingListRow.dataset['pickingListNumber'];

            pickingListRow.querySelectorAll('input[type=checkbox]').forEach(
                checkbox => {
                    checkbox.addEventListener('change', function (evt) {
                            const pickingListNumber = evt.target.parentElement.dataset['pickingListNumber'];

                            _self.setPickingListProductsChecked(pickingListNumber, evt.target.checked);
                        }
                    )
                }
            );

            pickingListRow.querySelectorAll('button').forEach(button => {
                button.addEventListener('click', function (evt) {
                    _self.setPickingListProductsVisible(pickingListNumber, evt.target.ariaPressed);
                });
            });
        });

        this.el.querySelector('[data-shipping-option-toggle][data-name="fix"]')
            .addEventListener('click', function (evt) {
                if (evt.target.checked) {
                    _self.el
                        .querySelector('[data-shipping-option-container][data-name="fix"]')
                        .classList.remove('hidden');
                } else {
                    _self.el
                        .querySelector('[data-shipping-option-container][data-name="fix"]')
                        .classList.add('hidden');
                }
            });
    }

    setPickingListProductsChecked(pickingListNumber, checked = true) {
        this.el.querySelectorAll(
            'div[data-picking-product][data-picking-list-number="' + pickingListNumber + '"] input[type=checkbox]'
        ).forEach(function (checkbox) {
            checkbox.checked = checked;
        });
    }

    setAllProductsChecked(checked = true) {
        this.el.querySelectorAll(
            'div[data-picking-product] input[type=checkbox]'
        ).forEach(function (checkbox) {
            checkbox.checked = checked;
        });
    }

    setPickingListProductsVisible(pickingListNumber, visible = 'true') {
        const addClass = visible === 'true' ? 'visible' : 'hidden';
        const removeClass = visible === 'true' ? 'hidden' : 'visible';

        this.el
            .querySelectorAll('[data-picking-product][data-picking-list-number="' + pickingListNumber + '"]')
            .forEach(pickingListProductRow => {
                pickingListProductRow.classList.add(addClass)
                pickingListProductRow.classList.remove(removeClass)
            });
    }

    order() {
        const productRows = this.el.querySelectorAll('[data-picking-product]:has(input:checked)')

        const lineItems = [];

        productRows.forEach(function (productRow) {
            lineItems.push({
                referencedId: productRow.dataset['productId'],
                sku: productRow.dataset['productSku'],
                pickingListNumber: productRow.dataset['pickingListNumber'],
                quantity: productRow.querySelector('input.product-quantity').value,
                netPrice: productRow.querySelector('input.product-net-price').value,
                grossPrice: productRow.querySelector('input.product-gross-price').value,
            });
        });

        const addresses = {
            billing: this.el.querySelector('select[data-billing-address] option:checked').dataset['address'],
            shipping: this.el.querySelector('select[data-shipping-address] option:checked').dataset['address'],
        };

        const options = {};
        this.el
            .querySelectorAll(
                'input:not([type=checkbox])[data-shipping-option],textarea[data-shipping-option], input[type=checkbox][data-shipping-option]:checked'
            ).forEach(function (optionInput) {
                const name = optionInput.dataset['name'];
                options[name] = optionInput.value;
            }
        );

        const url = window.router['frontend.account.order-picking-add-to-cart'];

        const offCanvasCartInstances = PluginManager.getPluginInstances('OffCanvasCart');
        Iterator.iterate(
            offCanvasCartInstances,
            instance => {
                instance.openOffCanvas(
                    url,
                    JSON.stringify(
                        {
                            redirectTo: 'frontend.cart.offcanvas',
                            lineItems: lineItems,
                            addresses: addresses,
                            options: options
                        }
                    ),
                    () => {
                        this.$emitter.publish('openOffCanvasCart');
                    }
                );
            }
        );
    }
}