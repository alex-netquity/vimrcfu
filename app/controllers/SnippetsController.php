<?php

use Vimrcfu\Comments\Comment;
use Vimrcfu\Snippets\Snippet;
use Vimrcfu\Snippets\SnippetsRepository;
use Vimrcfu\Users\User;

class SnippetsController extends \BaseController
{
    /**
   * @var Vimrc\Snippets\SnippetsRepository
   */
  private $repository;

  /**
   * @param Vimrcfu\Snippets\SnippetsRepository $repository
   */
  public function __construct(SnippetsRepository $repository)
  {
      $this->beforeFilter('auth', ['only' => ['create', 'store', 'edit', 'update']]);
      $this->repository = $repository;
  }

  /**
   * Displays all Snippets paginated.
   *
   * @return mixed
   */
  public function index()
  {
      $snippets = $this->repository->snippetsWithCommentsAndUsersPaginated();

      return View::make('snippets.index', compact('snippets'))
        ->withSnippetsCount(Snippet::remember(5)->get()->count())
        ->withCommentsCount(Comment::remember(5)->get()->count())
        ->withUsersCount(User::remember(5)->get()->count());
  }

  /**
   * Displays all Snippets ordered by total points and paginated.
   *
   * @return mixed
   */
  public function points()
  {
      return $this->getSortedSnippets('votes');
  }

  /**
   * Displays all Snippets ordered by number of comments and paginated.
   *
   * @return mixed
   */
  public function comments()
  {
      return $this->getSortedSnippets('comments');
  }

  /**
   * Gets view for showing all Snippets ordered by number of comments or total points and paginated.
   *
   * @return mixed
   */
  private function getSortedSnippets($sortBy)
  {
      switch ($sortBy) {
      case 'comments':
          $retrieveFunction = 'snippetsByComments';
          $title = 'Most Commented Snippets';
          break;
      case 'votes':
          $retrieveFunction = 'snippetsByPoints';
          $title = 'Top Voted Snippets';
          break;
      default:
          throw new InvalidArgumentException('$sortyBy argument not one of expected options.');
    }

      $page = Input::get('page', 1);
      $snippets = $this->repository->$retrieveFunction($page);
      $snippets = Paginator::make($snippets->items, $snippets->totalItems, 10);
      $topCommented = $this->repository->topCommented();

      return View::make('snippets.index')
      ->withSnippets($snippets)
      ->withSnippetsCount(Snippet::remember(5)->get()->count())
      ->withCommentsCount(Comment::remember(5)->get()->count())
      ->withUsersCount(User::remember(5)->get()->count())
      ->withTitle($title);
  }

  /**
   * Shows the form for creating a new Snippet.
   *
   * @return mixed
   */
  public function create()
  {
      return View::make('snippets.create')->withSnippet(new Snippet());
  }

  /**
   * Stores a new Snippet.
   *
   * @return mixed
   */
  public function store()
  {
      $snippet = $this->repository->create(Input::all());

      return Redirect::route('snippet.show', $snippet->id);
  }

  /**
   * Displays a Snippet.
   *
   * @param Vimrcfu\Snippets\Snippet $snippet
   * @return mixed
   */
  public function show(Snippet $snippet)
  {
      return View::make('snippets.show', compact('snippet'));
  }

  /**
   * Shows the form for editing a Snippet.
   *
   * @param  Vimrcfu\Snippets\Snippet $snippet
   * @return mixed
   */
  public function edit(Snippet $snippet)
  {
      if (Auth::user()->id != $snippet->user_id) {
          return Redirect::home();
      }

      return View::make('snippets.edit', compact('snippet'));
  }

  /**
   * Updates a Snippet in storage.
   *
   * @param  Vimrcfu\Snippets\Snippet $snippet
   * @return mixed
   */
  public function update(Snippet $snippet)
  {
      if (Auth::user()->id != $snippet->user_id) {
          return Redirect::home();
      }

      $this->repository->update($snippet, Input::all());

      return Redirect::route('snippet.show', $snippet->id);
  }
}
