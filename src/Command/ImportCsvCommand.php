<?php

namespace App\Command;

use App\Entity\Pokemon;
use App\Repository\PokemonTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import:csv',
    description: 'Importer une liste de pokemons',
)]
class ImportCsvCommand extends Command
{

    public function __construct(private EntityManagerInterface $entityManager, private PokemonTypeRepository $pokemonTypeRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('filePath', InputArgument::REQUIRED, 'Path to the CSV file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = $input->getArgument('filePath');
        if (pathinfo($filePath, PATHINFO_EXTENSION) !== 'csv') {
            $io->error("Le fichier n'est pas un CSV valide.");
            return Command::FAILURE;
        }

        if (($handle = fopen($filePath, 'r')) !== false) {
            $keys = fgetcsv($handle);
            while (($row = fgetcsv($handle)) !== false) {
                $data = array_combine($keys, $row);
                $pokemon = Pokemon::fromCsv(array_combine($keys, $row));
                $pokemon->addType($this->pokemonTypeRepository->findByNameOrCreate($data["Type 1"]));
                if (!empty($data["Type 2"])) {
                    $pokemon->addType($this->pokemonTypeRepository->findByNameOrCreate($data["Type 2"]));
                }
                $this->entityManager->persist($pokemon);
            }
            $this->entityManager->flush();
            fclose($handle);
        } else {
            $io->error("Impossible d'ouvrir le fichier CSV.");
        }
        $io->success('La liste des pokémons a été importée.');
        return Command::SUCCESS;
    }
}
