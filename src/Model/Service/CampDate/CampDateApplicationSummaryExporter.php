<?php

namespace App\Model\Service\CampDate;

use App\Model\Entity\Application;
use App\Model\Entity\CampDate;
use App\Model\Enum\Entity\ApplicationCustomerChannelEnum;
use App\Model\Enum\Entity\ContactRoleEnum;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Service\Application\ApplicationInvoiceNumberFormatterInterface;
use libphonenumber\PhoneNumberUtil;
use NumberFormatter;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Outputs application summary in a XLSX.
 */
class CampDateApplicationSummaryExporter implements CampDateApplicationSummaryExporterInterface
{
    private ApplicationRepositoryInterface $applicationRepository;

    private ApplicationInvoiceNumberFormatterInterface $invoiceNumberFormatter;

    private UrlGeneratorInterface $urlGenerator;

    private TranslatorInterface $translator;

    private RequestStack $requestStack;

    private PhoneNumberUtil $phoneNumberUtil;

    private string $dateTimeFormat;

    private string $dateFormat;

    private string $phoneNumberFormat;

    public function __construct(
        ApplicationRepositoryInterface             $applicationRepository,
        ApplicationInvoiceNumberFormatterInterface $invoiceNumberFormatter,
        UrlGeneratorInterface                      $urlGenerator,
        TranslatorInterface                        $translator,
        RequestStack                               $requestStack,
        PhoneNumberUtil                            $phoneNumberUtil,

        #[Autowire('%app.date_time_format%')]
        string $dateTimeFormat,

        #[Autowire('%app.date_format%')]
        string $dateFormat,

        #[Autowire('%app.phone_number_format%')]
        string $phoneNumberFormat,
    ) {
        $this->applicationRepository = $applicationRepository;
        $this->invoiceNumberFormatter = $invoiceNumberFormatter;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->requestStack = $requestStack;
        $this->phoneNumberUtil = $phoneNumberUtil;
        $this->dateTimeFormat = $dateTimeFormat;
        $this->dateFormat = $dateFormat;
        $this->phoneNumberFormat = $phoneNumberFormat;
    }

    /**
     * @inheritDoc
     */
    public function exportSummary(CampDate $campDate): string
    {
        $request = $this->requestStack->getCurrentRequest();
        $locale = $request->getLocale();
        $fmt = numfmt_create($locale, NumberFormatter::CURRENCY);

        $applications = $this->applicationRepository->findAcceptedByCampDate($campDate);
        $spreadsheet = new Spreadsheet();

        $this->addApplicationsSheet($spreadsheet, $fmt, $applications);
        $this->addApplicationCampersSheet($spreadsheet, $fmt, $applications);
        $this->addApplicationContactsSheet($spreadsheet, $applications);
        $this->addApplicationPurchasableItemsSheet($spreadsheet, $applications);
        $this->addApplicationAdminAttachmentsSheet($spreadsheet, $applications);

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $fileContents = ob_get_contents();
        ob_end_clean();

        return $fileContents;
    }

    /**
     * @inheritDoc
     */
    public static function getFileExtension(): string
    {
        return 'xlsx';
    }

    /**
     * @param Spreadsheet $spreadsheet
     * @param NumberFormatter $fmt
     * @param Application[] $applications
     * @return void
     */
    private function addApplicationsSheet(Spreadsheet $spreadsheet, NumberFormatter $fmt, array $applications): void
    {
        $applicationsSheet = $spreadsheet->getActiveSheet();
        $applicationsSheetTitle = $this->translator->trans('spreadsheet.camp_date_applications.title_applications');
        $applicationsSheet->setTitle($applicationsSheetTitle);

        // applications header

        $applicationsHeaderData = [
            $this->translator->trans('entity_attribute.application.simple_id'),
            $this->translator->trans('entity_attribute.application.invoice_number'),
            $this->translator->trans('entity_attribute.application.deposit'),
            $this->translator->trans('entity_attribute.application.price_without_deposit'),
            $this->translator->trans('entity_attribute.application.full_price_without_tax'),
            $this->translator->trans('entity_attribute.application.full_price'),
            $this->translator->trans('entity_attribute.application.deposit_until'),
            $this->translator->trans('entity_attribute.application.price_without_deposit_until'),
            $this->translator->trans('entity_attribute.application.completed_at'),
            $this->translator->trans('entity_attribute.application.payment_method'),
            $this->translator->trans('entity_attribute.application.customer_channel'),
            $this->translator->trans('entity_attribute.application.note'),
            $this->translator->trans('entity_attribute.application.email'),
            $this->translator->trans('entity_attribute.application.name_first'),
            $this->translator->trans('entity_attribute.application.name_last'),
            $this->translator->trans('entity_attribute.application.street'),
            $this->translator->trans('entity_attribute.application.town'),
            $this->translator->trans('entity_attribute.application.zip'),
            $this->translator->trans('entity_attribute.application.country'),
            $this->translator->trans('entity_attribute.application.business_name'),
            $this->translator->trans('entity_attribute.application.business_cin'),
            $this->translator->trans('entity_attribute.application.business_vat_id'),
        ];

        // application form field values header

        $applicationFormFieldValueOffset = 0;
        $applicationFormFieldValueOffsets = [];
        $applicationFormFieldValuesHeaderData = [];

        foreach ($applications as $application)
        {
            $applicationFormFieldValues = $application->getApplicationFormFieldValues();

            foreach ($applicationFormFieldValues as $applicationFormFieldValue)
            {
                $label = $applicationFormFieldValue->getLabel();

                if (!array_key_exists($label, $applicationFormFieldValueOffsets))
                {
                    $applicationFormFieldValuesHeaderData[] = $label;
                    $applicationFormFieldValueOffsets[$label] = $applicationFormFieldValueOffset;
                    $applicationFormFieldValueOffset++;
                }
            }
        }

        $applicationFormFieldValuesDataStart = count($applicationsHeaderData);

        // application attachments header

        $applicationAttachmentOffset = 0;
        $applicationAttachmentOffsets = [];
        $applicationAttachmentsHeaderData = [];

        foreach ($applications as $application)
        {
            $applicationAttachments = $application->getApplicationAttachments();

            foreach ($applicationAttachments as $applicationAttachment)
            {
                $label = $applicationAttachment->getLabel();

                if (!array_key_exists($label, $applicationAttachmentOffsets))
                {
                    $applicationAttachmentsHeaderData[] = $label;
                    $applicationAttachmentOffsets[$label] = $applicationAttachmentOffset;
                    $applicationAttachmentOffset++;
                }
            }
        }

        $applicationAttachmentsDataStart = $applicationFormFieldValuesDataStart + count($applicationFormFieldValueOffsets);

        // finalize application header

        $applicationsHeaderData = array_merge($applicationsHeaderData, $applicationFormFieldValuesHeaderData, $applicationAttachmentsHeaderData);
        $numberOfColumns = count($applicationsHeaderData);
        $applicationsData = [$applicationsHeaderData];

        // fill application columns

        foreach ($applications as $application)
        {
            // static application columns

            $currency = $application->getCurrency();
            $fullPrice = $application->getFullPrice();
            $fullDeposit = $application->getFullDeposit();
            $fullRest = $application->getFullRest();
            $fullPriceFormatted = numfmt_format_currency($fmt, $fullPrice, $currency);
            $fullDepositFormatted = numfmt_format_currency($fmt, $fullDeposit, $currency);
            $fullRestFormatted = numfmt_format_currency($fmt, $fullRest, $currency);
            $fullPriceWithoutTaxFormatted = $application->getTax() > 0
                ? numfmt_format_currency($fmt, $application->getFullPriceWithoutTax(), $currency)
                : ''
            ;

            $customerChannel = $application->getCustomerChannel();
            $customerChannelValue = '';

            if ($customerChannel !== null)
            {
                $customerChannelValue = $customerChannel === ApplicationCustomerChannelEnum::OTHER ?
                    $application->getCustomerChannelOther() :
                    $this->translator->trans('application_customer_channel.' . $customerChannel->value)
                ;
            }

            $country = $application->getCountry();
            $countryName = Countries::exists($country) ? Countries::getName($country) : '';
            $paymentMethodName = $this->translator->trans($application->getPaymentMethodLabel());

            $applicationRow = [
                $application->getSimpleId(),
                $this->invoiceNumberFormatter->getFormattedInvoiceNumber($application),
                $fullDepositFormatted,
                $fullRestFormatted,
                $fullPriceWithoutTaxFormatted,
                $fullPriceFormatted,
                $application->getDepositUntil()?->format($this->dateTimeFormat),
                $application->getPriceWithoutDepositUntil()?->format($this->dateTimeFormat),
                $application->getCompletedAt()?->format($this->dateTimeFormat),
                $paymentMethodName,
                $customerChannelValue,
                $application->getNote(),
                $application->getEmail(),
                $application->getNameFirst(),
                $application->getNameLast(),
                $application->getStreet(),
                $application->getTown(),
                $application->getZip(),
                $countryName,
                $application->getBusinessName(),
                $application->getBusinessCin(),
                $application->getBusinessVatId(),
            ];

            // application form field values

            foreach ($applicationFormFieldValueOffsets as $applicationFormFieldValueOffset)
            {
                $index = $applicationFormFieldValuesDataStart + $applicationFormFieldValueOffset;
                $applicationRow[$index] = '';
            }

            $applicationFormFieldValues = $application->getApplicationFormFieldValues();

            foreach ($applicationFormFieldValues as $applicationFormFieldValue)
            {
                $label = $applicationFormFieldValue->getLabel();

                if (array_key_exists($label, $applicationFormFieldValueOffsets))
                {
                    $applicationFormFieldValueOffset = $applicationFormFieldValueOffsets[$label];
                    $index = $applicationFormFieldValuesDataStart + $applicationFormFieldValueOffset;
                    $applicationRow[$index] = $applicationFormFieldValue->getValueAsString();
                }
            }

            // application attachments

            foreach ($applicationAttachmentOffsets as $applicationAttachmentOffset)
            {
                $index = $applicationAttachmentsDataStart + $applicationAttachmentOffset;
                $applicationRow[$index] = '';
            }

            $applicationAttachments = $application->getApplicationAttachments();

            foreach ($applicationAttachments as $applicationAttachment)
            {
                $label = $applicationAttachment->getLabel();

                if (array_key_exists($label, $applicationAttachmentOffsets))
                {
                    $applicationAttachmentOffset = $applicationAttachmentOffsets[$label];
                    $index = $applicationAttachmentsDataStart + $applicationAttachmentOffset;
                    $applicationRow[$index] = $this->urlGenerator->generate(
                        'admin_application_attachment',
                        ['id' => $applicationAttachment->getId()],
                        UrlGeneratorInterface::ABSOLUTE_URL,
                    );
                }
            }

            $emptyArray = array_fill(0, $numberOfColumns, ''); // avoids "holes" in the row array caused by missing values
            $applicationsData[] = $applicationRow + $emptyArray;
        }

        $applicationsSheet->fromArray($applicationsData);
        $this->convertUrlCellsToHyperlinks($applicationsSheet);
        $this->styleHeader($applicationsSheet);
        $this->setFirstCellAsSelected($applicationsSheet);
    }

    /**
     * @param Spreadsheet $spreadsheet
     * @param NumberFormatter $fmt
     * @param Application[] $applications
     * @return void
     */
    private function addApplicationCampersSheet(Spreadsheet $spreadsheet, NumberFormatter $fmt, array $applications): void
    {
        $applicationCampersSheet = $spreadsheet->createSheet();
        $applicationCampersSheetTitle = $this->translator->trans('spreadsheet.camp_date_applications.title_application_campers');
        $applicationCampersSheet->setTitle($applicationCampersSheetTitle);

        // application campers header

        $applicationCampersHeaderData = [
            $this->translator->trans('entity_attribute.application_camper.application'),
            $this->translator->trans('entity_attribute.application_camper.name_first'),
            $this->translator->trans('entity_attribute.application_camper.name_last'),
            $this->translator->trans('entity_attribute.application_camper.gender'),
            $this->translator->trans('entity_attribute.application_camper.national_identifier'),
            $this->translator->trans('entity_attribute.application_camper.born_at'),
            $this->translator->trans('entity_attribute.application_camper.dietary_restrictions'),
            $this->translator->trans('entity_attribute.application_camper.health_restrictions'),
            $this->translator->trans('entity_attribute.application_camper.medication'),
            $this->translator->trans('entity_attribute.application_camper.medical_diary'),
            $this->translator->trans('entity_attribute.application_camper.full_price'),
            $this->translator->trans('entity_attribute.application_camper.trip_location_there'),
            $this->translator->trans('entity_attribute.application_camper.trip_location_back'),
        ];

        // application camper form field values header

        $applicationCamperFormFieldValueOffset = 0;
        $applicationCamperFormFieldValueOffsets = [];
        $applicationCamperFormFieldValuesHeaderData = [];

        foreach ($applications as $application)
        {
            foreach ($application->getApplicationCampers() as $applicationCamper)
            {
                $applicationCamperFormFieldValues = $applicationCamper->getApplicationFormFieldValues();

                foreach ($applicationCamperFormFieldValues as $applicationCamperFormFieldValue)
                {
                    $label = $applicationCamperFormFieldValue->getLabel();

                    if (!array_key_exists($label, $applicationCamperFormFieldValueOffsets))
                    {
                        $applicationCamperFormFieldValuesHeaderData[] = $label;
                        $applicationCamperFormFieldValueOffsets[$label] = $applicationCamperFormFieldValueOffset;
                        $applicationCamperFormFieldValueOffset++;
                    }
                }
            }
        }

        $applicationCamperFormFieldValuesDataStart = count($applicationCampersHeaderData);

        // application camper attachments header

        $applicationCamperAttachmentOffset = 0;
        $applicationCamperAttachmentOffsets = [];
        $applicationCamperAttachmentsHeaderData = [];

        foreach ($applications as $application)
        {
            foreach ($application->getApplicationCampers() as $applicationCamper)
            {
                $applicationCamperAttachments = $applicationCamper->getApplicationAttachments();

                foreach ($applicationCamperAttachments as $applicationCamperAttachment)
                {
                    $label = $applicationCamperAttachment->getLabel();

                    if (!array_key_exists($label, $applicationCamperAttachmentOffsets))
                    {
                        $applicationCamperAttachmentsHeaderData[] = $label;
                        $applicationCamperAttachmentOffsets[$label] = $applicationCamperAttachmentOffset;
                        $applicationCamperAttachmentOffset++;
                    }
                }
            }
        }

        $applicationCamperAttachmentsDataStart = $applicationCamperFormFieldValuesDataStart + count($applicationCamperFormFieldValueOffsets);

        // finalize application camper header

        $applicationCampersHeaderData = array_merge(
            $applicationCampersHeaderData, $applicationCamperFormFieldValuesHeaderData, $applicationCamperAttachmentsHeaderData
        );

        $applicationCampersData = [$applicationCampersHeaderData];

        // fill application camper columns

        foreach ($applications as $application)
        {
            foreach ($application->getApplicationCampers() as $applicationCamper)
            {
                // static application camper columns

                $gender = $applicationCamper->getGender();
                $genderString = $this->translator->trans('gender_childish.' . $gender->value);

                $currency = $application->getCurrency();
                $fullPrice = $applicationCamper->getFullPrice();
                $fullPriceFormatted = numfmt_format_currency($fmt, $fullPrice, $currency);

                $tripLocationThere = $applicationCamper->getApplicationTripLocationPathThere();
                $tripLocationBack = $applicationCamper->getApplicationTripLocationPathBack();

                $applicationCamperRow = [
                    $application->getSimpleId(),
                    $applicationCamper->getNameFirst(),
                    $applicationCamper->getNameLast(),
                    $genderString,
                    $applicationCamper->getNationalIdentifier(),
                    $applicationCamper->getBornAt()->format($this->dateFormat),
                    $applicationCamper->getDietaryRestrictions(),
                    $applicationCamper->getHealthRestrictions(),
                    $applicationCamper->getMedication(),
                    $applicationCamper->getMedicalDiary(),
                    $fullPriceFormatted,
                    $tripLocationThere?->getLocation(),
                    $tripLocationBack?->getLocation(),
                ];

                // application camper form field values

                foreach ($applicationCamperFormFieldValueOffsets as $applicationCamperFormFieldValueOffset)
                {
                    $index = $applicationCamperFormFieldValuesDataStart + $applicationCamperFormFieldValueOffset;
                    $applicationCamperRow[$index] = '';
                }

                $applicationCamperFormFieldValues = $applicationCamper->getApplicationFormFieldValues();

                foreach ($applicationCamperFormFieldValues as $applicationCamperFormFieldValue)
                {
                    $label = $applicationCamperFormFieldValue->getLabel();

                    if (array_key_exists($label, $applicationCamperFormFieldValueOffsets))
                    {
                        $applicationCamperFormFieldValueOffset = $applicationCamperFormFieldValueOffsets[$label];
                        $index = $applicationCamperFormFieldValuesDataStart + $applicationCamperFormFieldValueOffset;
                        $applicationCamperRow[$index] = $applicationCamperFormFieldValue->getValueAsString();
                    }
                }

                // application camper attachments

                foreach ($applicationCamperAttachmentOffsets as $applicationCamperAttachmentOffset)
                {
                    $index = $applicationCamperAttachmentsDataStart + $applicationCamperAttachmentOffset;
                    $applicationCamperRow[$index] = '';
                }

                $applicationCamperAttachments = $applicationCamper->getApplicationAttachments();

                foreach ($applicationCamperAttachments as $applicationCamperAttachment)
                {
                    $label = $applicationCamperAttachment->getLabel();

                    if (array_key_exists($label, $applicationCamperAttachmentOffsets))
                    {
                        $applicationCamperAttachmentOffset = $applicationCamperAttachmentOffsets[$label];
                        $index = $applicationCamperAttachmentsDataStart + $applicationCamperAttachmentOffset;
                        $applicationCamperRow[$index] = $this->urlGenerator->generate(
                            'admin_application_attachment',
                            ['id' => $applicationCamperAttachment->getId()],
                            UrlGeneratorInterface::ABSOLUTE_URL,
                        );
                    }
                }

                $applicationCampersData[] = $applicationCamperRow;
            }
        }

        $applicationCampersSheet->fromArray($applicationCampersData);
        $this->convertUrlCellsToHyperlinks($applicationCampersSheet);
        $this->styleHeader($applicationCampersSheet);
        $this->setFirstCellAsSelected($applicationCampersSheet);
    }

    /**
     * @param Spreadsheet $spreadsheet
     * @param Application[] $applications
     * @return void
     */
    private function addApplicationContactsSheet(Spreadsheet $spreadsheet, array $applications): void
    {
        $contactsSheet = $spreadsheet->createSheet();
        $contactsSheetTitle = $this->translator->trans('spreadsheet.camp_date_applications.title_contacts');
        $contactsSheet->setTitle($contactsSheetTitle);

        // contacts header

        $contactsData = [[
            $this->translator->trans('entity_attribute.application_contact.application'),
            $this->translator->trans('entity_attribute.application_contact.email'),
            $this->translator->trans('entity_attribute.application_contact.name_first'),
            $this->translator->trans('entity_attribute.application_contact.name_last'),
            $this->translator->trans('entity_attribute.application_contact.phone_number'),
            $this->translator->trans('entity_attribute.application_contact.role'),
        ]];

        // fill contacts

        foreach ($applications as $application)
        {
            foreach ($application->getApplicationContacts() as $applicationContact)
            {
                $phoneNumber = $applicationContact->getPhoneNumber();
                $phoneNumberString = '';

                if ($phoneNumber !== null)
                {
                    $phoneNumberString = $this->phoneNumberUtil->format($phoneNumber, $this->phoneNumberFormat);
                }

                $role = $applicationContact->getRole();
                $roleValue = $role === ContactRoleEnum::OTHER ?
                    $applicationContact->getRoleOther() :
                    $this->translator->trans('contact_role.' . $role->value)
                ;

                $contactsData[] = [
                    $application->getSimpleId(),
                    $applicationContact->getEmail(),
                    $applicationContact->getNameFirst(),
                    $applicationContact->getNameLast(),
                    $phoneNumberString,
                    $roleValue,
                ];
            }
        }

        $contactsSheet->fromArray($contactsData);
        $this->styleHeader($contactsSheet);
        $this->setFirstCellAsSelected($contactsSheet);
    }

    /**
     * @param Spreadsheet $spreadsheet
     * @param Application[] $applications
     * @return void
     */
    public function addApplicationPurchasableItemsSheet(Spreadsheet $spreadsheet, array $applications): void
    {
        $purchasableItemsSheet = $spreadsheet->createSheet();
        $purchasableItemsSheetTitle = $this->translator->trans('spreadsheet.camp_date_applications.title_purchasable_items');
        $purchasableItemsSheet->setTitle($purchasableItemsSheetTitle);

        $purchasableItemsData = [];

        foreach ($applications as $application)
        {
            $applicationSimpleId = $application->getSimpleId();

            foreach ($application->getApplicationPurchasableItems() as $item)
            {
                $itemInstancesTotalAmount = $item->getInstancesTotalAmount();

                if ($itemInstancesTotalAmount <= 0)
                {
                    continue;
                }

                $itemLabel = $item->getLabel();

                if (!array_key_exists($itemLabel, $purchasableItemsData) || !array_key_exists('total_amount', $purchasableItemsData[$itemLabel]))
                {
                    $purchasableItemsData[$itemLabel]['total_amount'] = 0;
                }

                $purchasableItemsData[$itemLabel]['total_amount'] += $itemInstancesTotalAmount;

                foreach ($item->getApplicationPurchasableItemInstances() as $itemInstance)
                {
                    $itemInstanceAmount = $itemInstance->getAmount();

                    if ($itemInstanceAmount <= 0)
                    {
                        continue;
                    }

                    $stringApplication = $this->translator->trans('entity_attribute.application_purchasable_item.application');
                    $itemInstanceString = "$stringApplication: $applicationSimpleId";
                    $itemInstanceApplicationCamper = $itemInstance->getApplicationCamper();

                    if ($itemInstanceApplicationCamper !== null)
                    {
                        $applicationCamperName = $itemInstanceApplicationCamper->getNameFull();
                        $stringCamper = $this->translator->trans('entity_attribute.application_purchasable_item_instance.application_camper');
                        $itemInstanceString .= ", $stringCamper: $applicationCamperName";
                    }

                    $itemInstanceChosenVariantValuesAsString = $itemInstance->getChosenVariantValuesAsString();
                    $itemInstanceString .= sprintf(' - %sx', $itemInstanceAmount);

                    if (!empty($itemInstanceChosenVariantValuesAsString))
                    {
                        $itemInstanceString .= " $itemInstanceChosenVariantValuesAsString";
                    }

                    $purchasableItemsData[$itemLabel]['instances'][] = $itemInstanceString;
                }
            }
        }

        $purchasableItemsRowData = [];
        $headerRows = [];

        foreach ($purchasableItemsData as $itemLabel => $purchasableItemData)
        {
            $purchasableItemsRowData[] = [$purchasableItemData['total_amount'] . 'x ' . $itemLabel . ':'];
            $headerRows[] = array_key_last($purchasableItemsRowData) + 1;

            foreach ($purchasableItemData['instances'] as $itemInstanceString)
            {
                $purchasableItemsRowData[] = [$itemInstanceString];
            }

            $purchasableItemsRowData[] = []; // empty space after each purchasable item
        }

        foreach ($headerRows as $headerRow)
        {
            $cellRange = 'A' . $headerRow;
            $style = $purchasableItemsSheet->getStyle($cellRange);
            $this->applyHeaderStyle($style);
        }

        $purchasableItemsSheet->fromArray($purchasableItemsRowData);
        $this->setFirstCellAsSelected($purchasableItemsSheet);
    }

    /**
     * @param Spreadsheet $spreadsheet
     * @param Application[] $applications
     * @return void
     */
    public function addApplicationAdminAttachmentsSheet(Spreadsheet $spreadsheet, array $applications): void
    {
        $adminAttachmentsRowData = [];
        $insertedRows = 0;
        $headerRows = [];

        foreach ($applications as $application)
        {
            $applicationAdminAttachmentsRowData = [];

            foreach ($application->getApplicationAdminAttachments() as $applicationAdminAttachment)
            {
                $applicationAdminAttachmentsRowData[] = [
                    $applicationAdminAttachment->getLabel(),
                    $this->urlGenerator->generate(
                        'admin_application_admin_attachment_read',
                        ['id' => $applicationAdminAttachment->getId()],
                        UrlGeneratorInterface::ABSOLUTE_URL,
                    )
                ];
            }

            if (!empty($applicationAdminAttachmentsRowData))
            {
                $headerRows[] = $insertedRows + 1;
                $applicationAdminAttachmentsRowData[] = []; // empty space after each application
                $headerText = sprintf('%s %s (%s %s)',
                    $this->translator->trans('entity.application.singular'),
                    $application->getSimpleId(),
                    $this->translator->trans('entity_attribute.application.customer'),
                    $application->getNameFull()
                );

                array_unshift($applicationAdminAttachmentsRowData, [$headerText]);

                foreach ($applicationAdminAttachmentsRowData as $applicationAdminAttachmentRowData)
                {
                    $adminAttachmentsRowData[] = $applicationAdminAttachmentRowData;
                    $insertedRows++;
                }
            }
        }

        $adminAttachmentsSheet = $spreadsheet->createSheet();
        $adminAttachmentsSheetTitle = $this->translator->trans('spreadsheet.camp_date_applications.title_admin_attachments');
        $adminAttachmentsSheet->setTitle($adminAttachmentsSheetTitle);
        $adminAttachmentsSheet->fromArray($adminAttachmentsRowData);
        $this->convertUrlCellsToHyperlinks($adminAttachmentsSheet);

        $highestColumn = $adminAttachmentsSheet->getHighestColumn();

        foreach ($headerRows as $headerRow)
        {
            $cellRange = 'A' . $headerRow . ':' . $highestColumn . $headerRow;
            $style = $adminAttachmentsSheet->getStyle($cellRange);
            $this->applyHeaderStyle($style);
        }

        $this->setFirstCellAsSelected($adminAttachmentsSheet);
    }

    private function convertUrlCellsToHyperlinks(Worksheet $sheet): void
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $linkText = $this->translator->trans('spreadsheet.link');

        for ($row = 1; $row <= $highestRow; $row++)
        {
            for ($col = 'A'; $col <= $highestColumn; $col++)
            {
                $cell = $sheet->getCell($col . $row);
                $value = $cell->getValue();

                if (filter_var($value, FILTER_VALIDATE_URL))
                {
                    $cell->setHyperlink(new Hyperlink($value, $value));
                    $cell->setValue($linkText);
                    $cell->getStyle()->getFont()->setUnderline(true);
                    $cell->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_BLUE);
                }
            }
        }
    }

    private function applyHeaderStyle(Style $style): void
    {
        $style->getFont()
            ->setBold(true)
        ;

        $style->getBorders()
            ->getBottom()
            ->setBorderStyle(Border::BORDER_THICK)
            ->setColor(new Color(Color::COLOR_BLACK))
        ;
    }

    private function styleHeader(Worksheet $sheet): void
    {
        $firstRow = 1;
        $highestColumn = $sheet->getHighestColumn();
        $cellRange = 'A' . $firstRow . ':' . $highestColumn . $firstRow;
        $style = $sheet->getStyle($cellRange);
        $this->applyHeaderStyle($style);
    }

    private function setFirstCellAsSelected(Worksheet $sheet): void
    {
        $sheet->setSelectedCell('A1');
    }
}