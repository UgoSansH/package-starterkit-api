<?php

namespace AppBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Form\Traits\DataClassTrait;
use AppBundle\Definition\Definition;

/**
 * Notification type entity form type
 */
class NotificationTypeType extends AbstractType
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
            ->add('title', TextType::class)
            ->add('description', TextType::class)
            ->add('enabled', ChoiceType::class, [
                'required' => false,
                'choices'  => [0, 1]
            ])
            ->add('formats', ChoiceType::class, [
                'required' => true,
                'multiple' => true,
                'choices'  => $this->getTypeChoices()
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

}
