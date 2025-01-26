<?php

namespace App\Form;

use App\Elasticsearch\Genre\GenreClient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class MovieType extends AbstractType
{

    public function __construct(
        private readonly GenreClient $genreClient,
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $genres = $this->genreClient->findAll();

        $builder->add('title');
        $builder->add('release_year');
        $builder->add('genres', choiceType::class, [
            'choices' => $genres,
            'multiple' => true,
            'required' => false,
            'expanded' => false,
        ]);
        $builder->add('description', TextareaType::class);
        $builder->add('save', SubmitType::class);
    }
}