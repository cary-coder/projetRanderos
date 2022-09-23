<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('auteur')
            ->add('contenu', CKEditorType::class) // Ce champ sera remplacé par un éditeur WYSIWYG
            ->add('categorie')
            //ajout du champ image, il n'est pas lié à la base de données (mapped:false)
            //required false pour le rendre obligatoire
            ->add('images', FileType::class, [
                'label' =>false,
                'multiple' => true,
                "mapped" => false,
                "required"=> false
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label'=> 'nom', 
                'placeholder' => 'choisissez une categorie'
            ])

            ->add('envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
