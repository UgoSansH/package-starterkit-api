<?php

namespace AppBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Form\Traits\DataClassTrait;
use Ugosansh\Component\EntityManager\ManagerInterface;

/**
 * Calendar entity form type
 */
class CalendarType extends AbstractType
{
    use DataClassTrait;

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
            ->add('name', TextType::class)
            ->add('externalId', IntegerType::class, [
                'required' => false
            ])
            ->add('description', TextType::class, [
                'required' => false
            ])
            ->add('color', TextType::class)
            ->add('primary', ChoiceType::class, [
                'choices' => [0, 1]
            ])
            ->add('funder', EntityType::class, [
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
