<?php

namespace App\Form;

use App\Entity\Matiere;
use App\Form\QuestionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\CollectionType; 

class MatiereType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nameles', TextType::class, [
                'label' => 'Catégories',
                'label_attr' => ['class' => 'labelmat'],
            ])
            ->add('titleles', TextType::class, [
                'label' => 'Titre',
                'label_attr' => ['class' => 'labelmat'],
            ])
            ->add('fichier', FileType::class, [
                'label' => 'Fichier (.pdf/.docx/.xlsx)',
                'label_attr' => ['class' => 'labelmat'],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new file([
                        'maxSize' => '5000k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader un fichier .pdf, .docx, .xlsx valide'
                    ])
                ],
            ])
            ->add('video', FileType::class, [
                'label' => 'Télécharger la vidéo (MP4)',
                'label_attr' => ['class' => 'labelmat'],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5000M',
                        'mimeTypes' => [
                            'video/mp4',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une vidéo au format MP4',
                    ])
                ],
            ])
            ->add('questions', CollectionType::class, [
                'entry_type' => QuestionType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Questions',
                
                'required' => true,
                
            ])
        ;
            
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Matiere::class,
        ]);
    }
}
