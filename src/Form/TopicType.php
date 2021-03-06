<?php

namespace App\Form;

use App\Entity\Board;
use App\Entity\Post;
use App\Entity\Topic;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TopicType
 * @package App\Form
 */
class TopicType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            // Board
            ->add('board', EntityType::class, [
                'class' => Board::class,
                'label' => false,
            ])

            // Title
            ->add('title', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Titre',
                ]
            ])

            // First post
            ->add('post', PostType::class, [
                'label' => false,
                'mapped' => false
            ])

            // PRE_SET_DATA -> Removing board field if already set
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                /** @var Topic $topic */
                $topic = $event->getData();
                if ($topic->getBoard() instanceof Board) {
                    $event->getForm()->remove('board');
                }
            })

        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Topic::class,
        ]);
    }
}
