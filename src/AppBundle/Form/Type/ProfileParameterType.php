<?php

namespace AppBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Form\Traits\DataClassTrait;
use Ugosansh\Component\EntityManager\ManagerInterface;
use AppBundle\Definition\Definition;

/**
 * Profile entity form type
 */
class ProfileParameterType extends AbstractType
{
    use DataClassTrait;

    /**
     * @var Definition
     */
    protected $definition;

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('formats', ChoiceType::class, [
                'required' => true,
                'multiple' => true,
                'choices'  => $this->getTypeChoices()
            ])
            ->add('enabled', ChoiceType::class, [
                'choices' => [0, 1]
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

    protected function getTypeChoices()
    {
        $choices = $this->definition->get('notification_format');

        return array_combine($choices, $choices);
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
     * Set definition
     *
     * @param Definition $definition
     *
     * @return self
     */
    public function setDefinition(Definition $definition): self
    {
        $this->definition = $definition;

        return $this;
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
