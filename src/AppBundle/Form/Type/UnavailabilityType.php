<?php

namespace AppBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Form\Traits\DataClassTrait;
use Ugosansh\Component\EntityManager\ManagerInterface;

/**
 * Unavailability entity form type
 */
class UnavailabilityType extends AbstractType
{
    use DataClassTrait;

    /**
     * @var ManagerInterface
     */
    protected $profileManager;

    /**
     * @var ManagerInterface
     */
    protected $calendarManager;

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('dateStart', DateType::class, [
                'widget' => 'single_text',
                'format' => "yyyy-MM-dd"
            ])
            ->add('dateEnd', DateType::class, [
                'required' => false,
                'widget'   => 'single_text',
                'format'   => "yyyy-MM-dd"
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
     * Set calendarManager
     *
     * @param ManagerInterface $calendarManager
     *
     * @return self
     */
    public function setCalendarManager(ManagerInterface $calendarManager): self
    {
        $this->calendarManager = $calendarManager;

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
