<?php

namespace App\Controller\Admin\Fragment;

use App\Controller\AbstractController;
use App\Library\Data\Admin\FormFieldData;
use App\Model\Enum\Entity\FormFieldTypeEnum;
use App\Service\Form\Type\Admin\FormFieldType;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED', statusCode: 403)]
class FormTypeController extends AbstractController
{
    #[IsGranted(new Expression('is_granted("form_field_create") or is_granted("form_field_edit")'))]
    #[Route('/admin/fragment/form-field-type', name: 'admin_fragment_form_field_type')]
    public function formField(Request $request): JsonResponse
    {
        $typeValue = $request->query->get('type', '');
        $options = $request->query->all('options');

        $type = FormFieldTypeEnum::tryFrom($typeValue);
        $data = new FormFieldData(null, true);

        try
        {
            $data->setType($type, $options);
        }
        catch (InvalidArgumentException)
        {
            $errorMessage = $this->trans('api.fragment.form.error.invalid_form_field_options');
            return new JsonResponse(['message' => $errorMessage], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $form = $this->createForm(FormFieldType::class, $data);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.form_field.button']);
        $formView = $this->renderView('_fragment/_form/_form_basic.html.twig', [
            'form' => $form->createView(),
        ]);

        return new JsonResponse(['form_field_type' => $formView]);
    }
}