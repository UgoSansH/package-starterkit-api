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
 * Event entity form type
 */
class EventType extends AbstractType
{
    use DataClassTrait;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

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
            ->add('description', TextType::class, [
                'required' => false
            ])
            ->add('visible', ChoiceType::class, [
                'choices'  => [0, 1],
                'required' => false
            ])
            ->add('dateStart', DateType::class, [
                'widget' => 'single_text',
                'format' => "yyyy-MM-dd HH:mm:ss"
            ])
            ->add('dateEnd', DateType::class, [
                'widget' => 'single_text',
                'format' => "yyyy-MM-dd HH:mm:ss"
            ])
            ->add('parameters', ArrayMapType::class, [
                'required' => false
            ])
            ->add('calendar', EntityType::class, [
                'class'        => $this->calendarManager->getEntityClass(),
                'choice_label' => 'name',
                'choices'      => $this->calendarManager->findBy([], ['id' => 'asc'])
            ])
            ->add('superEvent', EntityType::class, [
                'required'     => false,
                'class'        => $this->eventManager->getEntityClass(),
                'choice_label' => 'name',
                'choices'      => $this->eventManager->findBy([], ['id' => 'asc'])
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
     * Set eventManager
     *
     * @param ManagerInterface $eventManager
     *
     * @return self
     */
    public function setEventManager(ManagerInterface $eventManager): self
    {
        $this->eventManager = $eventManager;

        return $this;
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

}
