<?php declare(strict_types=1);

namespace MtoOrderPicking;

use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\System\CustomField\CustomFieldTypes;

class MtoOrderPicking extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        /* @var $customFieldRepository EntityRepository */
        $customFieldRepository = $this->container->get('custom_field_set.repository');

        $customFieldRepository->create([
            [
                'name' => 'picking_list_options',
                'config' => [
                    'label' => [
                        'en-GB' => 'Picking list order options',
                        'de-DE' => 'Komissionierung',
                        Defaults::LANGUAGE_SYSTEM => "Picking list order options"
                    ]
                ],
                'active' => true,
                'position' => 0,
                'customFields' => [
                    [
                        'name' => 'customer',
                        'type' => CustomFieldTypes::TEXT,
                        'config' => [
                            'label' => [
                                'en-GB' => 'Customer',
                                'de-DE' => 'Auftraggerber',
                                Defaults::LANGUAGE_SYSTEM => "Customer"
                            ],
                            'customFieldPosition' => 1
                        ]
                    ],
                    [
                        'name' => 'phone',
                        'type' => CustomFieldTypes::TEXT,
                        'config' => [
                            'label' => [
                                'en-GB' => 'Phone',
                                'de-DE' => 'Telefon',
                                Defaults::LANGUAGE_SYSTEM => "Phone"
                            ],
                            'customFieldPosition' => 1
                        ]
                    ],
                    [
                        'name' => 'notes',
                        'type' => CustomFieldTypes::TEXT,
                        'config' => [
                            'label' => [
                                'en-GB' => 'Notes',
                                'de-DE' => 'Anmerkungen',
                                Defaults::LANGUAGE_SYSTEM => "Notes"
                            ],
                            'customFieldPosition' => 1
                        ]
                    ],
                    [
                        'name' => 'delivery_date',
                        'type' => CustomFieldTypes::DATE,
                        'config' => [
                            'label' => [
                                'en-GB' => 'Delivery date',
                                'de-DE' => 'Lieferdatum',
                                Defaults::LANGUAGE_SYSTEM => "Delivery date"
                            ],
                            'customFieldPosition' => 1
                        ]
                    ],
                    [
                        'name' => 'fix',
                        'type' => CustomFieldTypes::CHECKBOX,
                        'config' => [
                            'label' => [
                                'en-GB' => 'Fixed date',
                                'de-DE' => 'Fixtermin',
                                Defaults::LANGUAGE_SYSTEM => "Fixed date"
                            ],
                            'customFieldPosition' => 1
                        ]
                    ],
                    [
                        'name' => 'neutral',
                        'type' => CustomFieldTypes::CHECKBOX,
                        'config' => [
                            'label' => [
                                'en-GB' => 'Neutral Shipping',
                                'de-DE' => 'Neutraler Versand',
                                Defaults::LANGUAGE_SYSTEM => "Neutral Shipping"
                            ],
                            'customFieldPosition' => 1
                        ]
                    ],
                    [
                        'name' => 'avis',
                        'type' => CustomFieldTypes::CHECKBOX,
                        'config' => [
                            'label' => [
                                'en-GB' => 'Avis',
                                'de-DE' => 'Avis',
                                Defaults::LANGUAGE_SYSTEM => "Avis"
                            ],
                            'customFieldPosition' => 1
                        ]
                    ],
                ],
                'relations' => [
                    ['entityName' => 'order']
                ]
            ]
        ], $installContext->getContext());

        parent::install($installContext);
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        /* @var $customFieldRepository EntityRepository */
        $customFieldRepository = $this->container->get('custom_field_set.repository');

        $customFieldsCriteria = new Criteria();
        $customFieldsCriteria->addFilter(new EqualsFilter('name', 'picking_list_options'));
        $customFieldsId = $customFieldRepository
            ->searchIds($customFieldsCriteria, $uninstallContext->getContext())
            ->firstId();

        if ($customFieldsId) {
            $customFieldRepository->delete([['id' => $customFieldsId]], $uninstallContext->getContext());
        }

        parent::uninstall($uninstallContext);
    }
}
