<?php

namespace App\Controller;

use Twig\Environment;
use App\Entity\Expense;
use App\Form\ExpenseFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ExpenseController extends AbstractController
{
    /*
    #[Route('/expense', name: 'app_expense')]
    public function createExpense(ManagerRegistry $doctrine, ValidatorInterface $validator): Response
    {
        $entityManager = $doctrine->getManager();

        $expense = new Expense();
        $expense->setName('Keyboard');
        $expense->setAmount(250);

        $date = date_create();
        $expense->setExpenseDate($date);

        $errors = $validator->validate($expense);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }

        // tell Doctrine you want to (eventually) save the Expense (no queries yet)
        $entityManager->persist($expense);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new expense with id '.$expense->getId());
    }
    */
    /*
    #[Route('/expense', name: 'app_expense')]
    public function createExpense(Request $request, PersistenceManagerRegistry $doctrine, ValidatorInterface $validator): Response
    {
        $entityManager = $doctrine->getManager();
        $form = $this->createFormBuilder()
                ->add('name', TextType::class)
                ->add('amount', NumberType::class)
                ->add('expense_date', DateTimeType::class, [
                    'attr' => [
                        'class' => 'btn btn-success float-right'
                    ]
                ])
                ->add('submit', SubmitType::class, [
                    'attr' => [
                        'class' => 'btn btn-success float-right'
                    ]
                ])
                ->getForm();

            $form->handleRequest($request);
            if($form->isSubmitted()) {
                $data = $form->getData();

                $expense = new Expense();
                $expense->setName($data['name']);
                $expense->setAmount($data['amount']);
                $expense->setExpenseDate($data['expense_date']);

                $errors = $validator->validate($expense);
                if (count($errors) > 0) {
                    return new Response((string) $errors, 400);
                }
                
                $entityManager->persist($expense);
                $entityManager->flush();

                return $this->redirectToRoute('expense_show', ['id' => $expense->getId()]);
                
            }
        return $this->render('expense/index.html.twig', ['form' => $form->CreateView(), 'controller_name' => 'ExpenseController']);
    }
    */

    #[Route('/expense/new', name: 'app_expense')]
    public function createExpense(Environment $twig, Request $request, PersistenceManagerRegistry $doctrine, ValidatorInterface $validator): Response
    {
        $entityManager = $doctrine->getManager();
        $expense = new Expense();

        $form = $this->createForm(ExpenseFormType::class, $expense);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($expense);
            $entityManager->flush();

            return new Response('Expense number ' . $expense->getId() . ' created!');
        }

        return new Response($twig->render('expense/show.html.twig', [
           'expense_form' => $form->createView() 
        ]));

    }

    #[Route('/expense/{id}', name: 'expense_show')]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $expense = $doctrine->getRepository(Expense::class)->find($id);

        if (!$expense) {
            throw $this->createNotFoundException(
                'No expense found for id '.$id
            );
        }

        return new Response('expense name: '.$expense->getName());
    }
    
    #[Route('/expense/edit/{id}', name: 'expense_edit')]
    public function update(Environment $twig, Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $expense = $entityManager->getRepository(Expense::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $form = $this->createForm(ExpenseFormType::class, $expense, array('method' => 'PUT'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->$entityManager->flush();
        }

        return new Response($twig->render('expense/show.html.twig', [
            'expense_form' => $form->createView() 
         ]));
    }

    /*
    #[Route('/expense/edit/{id}', name: 'expense_edit')]
    public function update(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $expense = $entityManager->getRepository(Expense::class)->find($id);

        if (!$expense) {
            throw $this->createNotFoundException(
                'No expense found for id '.$id
            );
        }

        $expense->setName('New expense name!');
        $entityManager->flush();

        return $this->redirectToRoute('expense_show', [
            'id' => $expense->getId()
        ]);
    }
    */

    #[Route('/expense/delete/{id}', name: 'expense_edit')]
    public function remove(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $expense = $entityManager->getRepository(Expense::class)->find($id);

        if (!$expense) {
            throw $this->createNotFoundException(
                'No expense found for id '.$id
            );
        }

        $entityManager->remove($expense);
        $entityManager->flush();

        return new Response('Removed expense with id '.$id);
    }
}