<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

class PostType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add("title", TextType::class, [
            "label" => "Titre",
            "required" => false,
            "constraints" => [new Length(["min" => 0, "max" => 150,  "minMessage" => "Le titre ne doit pas faire moins de x caractères", "maxMessage" => "Le titre ne doit pas faire plus de 320 caractères"])],
        ])

        ->add("content", TextareaType::class, [
            "label" => "Contenu",
            "required" => true,
            "constraints" => [
                new Length(["min" => 5, "max" => 320,  "minMessage" => "Le contenu doit faire plus de 5 caractères", "maxMessage" => "Le contenu ne doit pas faire plus de 320 caractères"]),
                new NotBlank(["message" => 'Le contenu ne doit pas être vide !'])
            ]
        ])

        ->add("image", UrlType::class, [
            "label" => "URL de l'image",
            "required" => false,
            'constraints' => [new Url(['message' => "L'image doit être une URL valide "])],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => Post::class,
            'csrf_protection' => true,
            // the name of the hidden HTML field that stores the token
            'csrf_field_name' => '_token',
            // an arbitrary string used to generate the value of the token
            // using a different string for each form improves its security
            'csrf_token_id'   => 'task_item',
        ]);
    }
}

