<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\CampSearchData;
use App\Library\Enum\Search\Data\Admin\CampSortEnum;
use App\Model\Entity\CampCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Admin camp search.
 */
class CampSearchType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        array_unshift($options['choices_camp_categories'], false);

        $builder
            ->add('phrase', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label' => 'form.admin.camp_search.phrase',
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => CampSortEnum::class,
                'label'        => 'form.admin.camp_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice)
                {
                    CampSortEnum::CREATED_AT_DESC => 'form.admin.camp_search.sort_by.options.created_at_desc',
                    CampSortEnum::CREATED_AT_ASC  => 'form.admin.camp_search.sort_by.options.created_at_asc',
                    CampSortEnum::NAME_ASC        => 'form.admin.camp_search.sort_by.options.name_asc',
                    CampSortEnum::NAME_DESC       => 'form.admin.camp_search.sort_by.options.name_desc',
                    CampSortEnum::URL_NAME_ASC    => 'form.admin.camp_search.sort_by.options.url_name_asc',
                    CampSortEnum::URL_NAME_DESC   => 'form.admin.camp_search.sort_by.options.url_name_desc',
                    CampSortEnum::PRIORITY_DESC   => 'form.admin.camp_search.sort_by.options.priority_desc',
                },
            ])
            ->add('age', IntegerType::class, [
                'attr' => [
                    'min' => 1,
                ],
                'required' => false,
                'label'    => 'form.admin.camp_search.age',
            ])
            ->add('from', DateType::class, [
                'required' => false,
                'widget'   => 'single_text',
                'input'    => 'datetime_immutable',
                'label'    => 'form.admin.camp_search.from',
            ])
            ->add('to', DateType::class, [
                'required' => false,
                'widget'   => 'single_text',
                'input'    => 'datetime_immutable',
                'label'    => 'form.admin.camp_search.to',
            ])
            ->add('campCategory', ChoiceType::class, [
                'choices'      => $options['choices_camp_categories'],
                'choice_label' => function (false|CampCategory $campCategory): string
                {
                    if ($campCategory === false)
                    {
                        return $this->translator->trans('search.item_no_reference.female');
                    }

                    return $campCategory->getPath();
                },
                'placeholder'               => 'form.common.choice.irrelevant',
                'required'                  => false,
                'label'                     => 'form.admin.camp_search.camp_category',
                'choice_translation_domain' => false,
            ])
            ->add('isOpenOnly', ChoiceType::class, [
                'placeholder' => 'form.common.choice.irrelevant',
                'choices'     => [
                    'form.common.choice.yes' => true,
                    'form.common.choice.no'  => false,
                ],
                'required' => false,
                'label'    => 'form.admin.camp_search.is_open_only',
            ])
            ->add('isFeatured', ChoiceType::class, [
                'placeholder' => 'form.common.choice.irrelevant',
                'choices'     => [
                    'form.common.choice.yes' => true,
                    'form.common.choice.no'  => false,
                ],
                'required' => false,
                'label'    => 'form.admin.camp_search.is_featured',
            ])
            ->add('isHidden', ChoiceType::class, [
                'placeholder' => 'form.common.choice.irrelevant',
                'choices'     => [
                    'form.common.choice.yes' => true,
                    'form.common.choice.no'  => false,
                ],
                'required' => false,
                'label'    => 'form.admin.camp_search.is_hidden',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'              => CampSearchData::class,
            'block_prefix'            => 'admin_camp_search',
            'choices_camp_categories' => [],
            'csrf_protection'         => false,
            'method'                  => 'GET',
            'allow_extra_fields'      => true,
        ]);

        $resolver->setAllowedTypes('choices_camp_categories', ['array']);
    }
}