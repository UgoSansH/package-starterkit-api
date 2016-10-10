<?php

namespace AppBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Form\Traits\DataClassTrait;
use Ugosansh\Component\EntityManager\ManagerInterface;

/**
 * Notification entity form type
 */
class NotificationType extends AbstractType
{
    use DataClassTrait;

    /**
     * @var ManagerInterface
     */
    protected $typeManager;

    /**
     * @var ManagerInterface
     */
    protected $profileManager;

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('source', TextType::class)
            ->add('icon', TextType::class, [
                'required' => false
            ])
            ->add('title', TextType::class)
            ->add('body', TextType::class, [
                'required' => false
            ])
            ->add('target', TextType::class, [
                'required' => false
            ])
            ->add('data', TextType::class, [
                'required' => false
            ])
            ->add('tags', TextType::class, [
                'required' => false
            ])
            ->add('actions', TextType::class, [
                'required' => false
            ])
            ->add('dateNotify', DateTimeType::class, [
                'required'    => false,
                'date_widget' => 'single_text',
                'date_format' => 'dd/MM/yyyy-hh:mm'
            ])
            ->add('dateRenotify', DateTimeType::class, [
                'required'    => false,
                'date_widget' => 'single_text',
                'date_format' => 'dd/MM/yyyy-hh:mm'
            ])
            ->add('dateSeen', DateTimeType::class, [
                'required'    => false,
                'date_widget' => 'single_text',
                'date_format' => 'dd/MM/yyyy-hh:mm'
            ])
            ->add('dateRead', DateTimeType::class, [
                'required'    => false,
                'date_widget' => 'single_text',
                'date_format' => 'dd/MM/yyyy-hh:mm'
            ])
            ->add('type', EntityType::class, [
                'class'        => $this->typeManager->getEntityClass(),
                'choice_label' => 'title',
                'choices'      => $this->typeManager->findBy([], ['id' => 'asc'])
            ])
            ->add('profile', EntityType::class, [
                'class'        => $this->profileManager->getEntityClass(),
                'choice_label' => 'name',
                'choices'      => $this->profileManager->findBy([], ['id' => 'asc'])
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->dataClass
        ]);
    }

    /**
     * Set typeManager
     *
     * @param ManagerInterface $typeManager
     *
     * @return self
     */
    public function setTypeManager(ManagerInterface $typeManager): self
    {
        $this->typeManager = $typeManager;

        return $this;
    }

    /**
     * Set profileManager
     *
     * @param ManagerInterface $profileManager
     *
     * @return self
     */
    public function setProfileManager(ManagerInterface $profileManager): self
    {
        $this->profileManager = $profileManager;

        return $this;
    }

}
