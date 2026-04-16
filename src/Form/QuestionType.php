<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class QuestionType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('question', TextType::class, [
                'label' => 'Question',
                'label_attr' => ['class' => 'labelmat'],
            ])
            ->add('optionA', TextType::class, [
                'label' => 'Option A',
                'label_attr' => ['class' => 'labelmat'],
            ])
            ->add('optionB', TextType::class, [
                'label' => 'Option B',
                'label_attr' => ['class' => 'labelmat'],
            ])
            ->add('optionC', TextType::class, [
                'label' => 'Option C',
                'label_attr' => ['class' => 'labelmat'],
            ])
            ->add('optionD', TextType::class, [
                'label' => 'Option D',
                'label_attr' => ['class' => 'labelmat'],
            ])
            ->add('correctOption', ChoiceType::class, [
                'label' => 'Bonne réponse',
                'label_attr' => ['class' => 'labelmat'],
                'choices' => [
                    'Option A' => 'optionA',
                    'Option B' => 'optionB',
                    'Option C' => 'optionC',
                    'Option D' => 'optionD',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}