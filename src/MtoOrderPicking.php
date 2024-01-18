<?php declare(strict_types=1);

namespace MtoOrderPicking;

use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
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
                            'customFieldPosition' => 10
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
                            'customFieldPosition' => 20
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
                            'customFieldPosition' => 30
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
                            'customFieldPosition' => 40
                        ]
                    ],
                    [
                        'name' => 'fix',
                        'type' => CustomFieldTypes::DATETIME,
                        'config' => [
                            'label' => [
                                'en-GB' => 'Fixed',
                                'de-DE' => 'Fixtermin',
                                Defaults::LANGUAGE_SYSTEM => "Fixed"
                            ],
                            'time_24h' => true,
                            'customFieldPosition' => 50
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
                            'customFieldPosition' => 60
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
                            'customFieldPosition' => 70
                        ]
                    ]
                ],
                'relations' => [
                    ['entityName' => 'order']
                ]
            ],
            [
                'name' => 'order_export',
                'config' => [
                    'label' => [
                        'en-GB' => 'Export',
                        'de-DE' => 'Export',
                        Defaults::LANGUAGE_SYSTEM => "Export"
                    ]
                ],
                'active' => true,
                'position' => 1,
                'customFields' => [
                    [
                        'name' => 'exported',
                        'type' => CustomFieldTypes::CHECKBOX,
                        'config' => [
                            'label' => [
                                'en-GB' => 'Exported',
                                'de-DE' => 'Exportiert',
                                Defaults::LANGUAGE_SYSTEM => "Exported"
                            ],
                            'customFieldPosition' => 80
                        ]
                    ],
                    [
                        'name' => 'order_export_number',
                        'type' => CustomFieldTypes::TEXT,
                        'config' => [
                            'label' => [
                                'en-GB' => 'Number',
                                'de-DE' => 'Nummer',
                                Defaults::LANGUAGE_SYSTEM => "Number"
                            ],
                            'customFieldPosition' => 90
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
        $customFieldsCriteria->addFilter(
            new EqualsAnyFilter('name', ['picking_list_options', 'order_export'])
        );
        $customFieldsId = $customFieldRepository
            ->searchIds($customFieldsCriteria, $uninstallContext->getContext())
            ->firstId();

        if ($customFieldsId) {
            $customFieldRepository->delete([['id' => $customFieldsId]], $uninstallContext->getContext());
        }

        parent::uninstall($uninstallContext);
    }
}
