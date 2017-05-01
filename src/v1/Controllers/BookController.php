<?php
namespace App\v1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Models\Entity\Book;

/**
 * Controller v1 de livros
 */
class BookController {

    /**
     * Container Class
     * @var [object]
     */
    private $container;

    /**
     * Undocumented function
     * @param [object] $container
     */
    public function __construct($container) {
        $this->container = $container;
    }
    
    /**
     * Listagem de Livros
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function listBook($request, $response, $args) {
        $entityManager = $this->container->get('em');
        $booksRepository = $entityManager->getRepository('App\Models\Entity\Book');
        $books = $booksRepository->findAll();
        $return = $response->withJson($books, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;        
    }
    
    /**
     * Cria um livro
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function createBook($request, $response, $args) {
        $params = (object) $request->getParams();
        /**
         * Pega o Entity Manager do nosso Container
         */
        $entityManager = $this->container->get('em');
        /**
         * Instância da nossa Entidade preenchida com nossos parametros do post
         */
        $book = (new Book())->setName($params->name)
            ->setAuthor($params->author);
        
        /**
         * Registra a criação do livro
         */
        $logger = $this->container->get('logger');
        $logger->info('Book Created!', $book->getValues());

        /**
         * Persiste a entidade no banco de dados
         */
        $entityManager->persist($book);
        $entityManager->flush();
        $return = $response->withJson($book, 201)
            ->withHeader('Content-type', 'application/json');
        return $return;       
    }

    /**
     * Exibe as informações de um livro 
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function viewBook($request, $response, $args) {

        $id = (int) $args['id'];

        $entityManager = $this->container->get('em');
        $booksRepository = $entityManager->getRepository('App\Models\Entity\Book');
        $book = $booksRepository->find($id); 

        /**
         * Verifica se existe um livro com a ID informada
         */
        if (!$book) {
            $logger = $this->container->get('logger');
            $logger->warning("Book {$id} Not Found");
            throw new \Exception("Book not Found", 404);
        }    

        $return = $response->withJson($book, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;   
    }

    /**
     * Atualiza um Livro
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function updateBook($request, $response, $args) {

        $id = (int) $args['id'];

        /**
         * Encontra o Livro no Banco
         */ 
        $entityManager = $this->container->get('em');
        $booksRepository = $entityManager->getRepository('App\Models\Entity\Book');
        $book = $booksRepository->find($id);   

        /**
         * Verifica se existe um livro com a ID informada
         */
        if (!$book) {
            $logger = $this->container->get('logger');
            $logger->warning("Book {$id} Not Found");
            throw new \Exception("Book not Found", 404);
        }  

        /**
         * Atualiza e Persiste o Livro com os parâmetros recebidos no request
         */
        $book->setName($request->getParam('name'))
            ->setAuthor($request->getParam('author'));

        /**
         * Persiste a entidade no banco de dados
         */
        $entityManager->persist($book);
        $entityManager->flush();        
        
        $return = $response->withJson($book, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;       
    }

    /**
     * Deleta um Livro
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function deleteBook($request, $response, $args) {

        $id = (int) $args['id'];

        /**
         * Encontra o Livro no Banco
         */ 
        $entityManager = $this->container->get('em');
        $booksRepository = $entityManager->getRepository('App\Models\Entity\Book');
        $book = $booksRepository->find($id);   

        /**
         * Verifica se existe um livro com a ID informada
         */
        if (!$book) {
            $logger = $this->container->get('logger');
            $logger->warning("Book {$id} Not Found");
            throw new \Exception("Book not Found", 404);
        }  

        /**
         * Remove a entidade
         */
        $entityManager->remove($book);
        $entityManager->flush(); 
        $return = $response->withJson(['msg' => "Deletando o livro {$id}"], 204)
            ->withHeader('Content-type', 'application/json');
        return $return;    
    }
    
}