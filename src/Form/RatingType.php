<?php

namespace App\Form;

use App\Entity\BookRating;
use App\Entity\Book;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RatingType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('rating', ChoiceType::class, [
        'choices' => [
          '1' => 1,
          '2' => 2,
          '3' => 3,
          '4' => 4,
          '5' => 5,
        ],
      ])
      ->add('book', EntityType::class, [
        'class' => Book::class,
        'choice_label' => 'title',
      ])
      ->add('user', EntityType::class, [
        // looks for choices from this entity
        'class' => User::class,
        // uses the User.username property as the visible option string
        'choice_label' => 'username',
        // used to render a select box, check boxes or radios
        // 'multiple' => true,
        // 'expanded' => true,
      ])
      ->add('save', SubmitType::class);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => BookRating::class,
    ]);
  }
}
