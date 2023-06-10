@extends('layout.main')

@section('title', 'Detail Article')

@section('content')

<main id="main">
  <!-- ======= Breadcrumbs ======= -->
  <section id="breadcrumbs" class="breadcrumbs">
    <div class="container">
    </div>
  </section><!-- End Breadcrumbs -->

  <!-- ======= Blog Single Section ======= -->
  <section id="blog" class="blog">
    <div class="container" data-aos="fade-up">

      <div class="row d-flex justify-content-center align-items-center">

        <div class="col-lg-8 entries">

          <article class="entry entry-single">

            @if ($article->image != NULL)
            <div class="entry-img">
              <img src="{{ asset('storage/photo/'.$article->image)}}" alt="" class="img-fluid" width="100%">
            </div>
            @endif

            <h2 class="entry-title">
              <a href="#">{{ $article->title }}</a>
            </h2>

            <div class="entry-meta">
              @php
              $userClap = Auth::check() ? $article->claps->where('user_id', Auth::user()->id)->first() : null;
              $clapCount = $article->claps->count();
              @endphp
              <ul>
                <li class="d-flex align-items-center"><i class="bi bi-person"></i> <a href="{{ route('profile', $article->user->username) }}">{{ $article->user->name }}</a></li>
                <li class="d-flex align-items-center"><i class="bi bi-clock"></i> <a class="nav-link disabled" href="#"><time datetime="2020-01-01">{{ $article->created_at->format('M d, Y') }}</time></a></li>
                <li class="d-flex align-items-center"> <a href="/clap/{{ $article->id }}" class="{{ $userClap ? ' text-primary' : '' }}"><i class="fa fa-hands-clapping"></i>{{ $clap }} Clap</a></li>
                @if (Auth::check())
                <li class="d-flex align-items-center">
                  @if ($article->bookmarkByUser(Auth::user(), $article->id)->exists())
                  <i class="bi bi-bookmark-fill"></i>
                  @else
                  <i class="bi bi-bookmark"></i>
                  @endif
                  <a href="{{ route('bookmark.add') }}" onclick="showBookmarkModal('{{ $article->id }}', event)" id="bookmarkLink" class="bookmark-btn">
                    Bookmark
                  </a>
                </li>
                @endif
              </ul>
            </div>

            <div class="entry-content">
              <p>{!! $article->content !!}</p>
            </div>

            <div class="entry-footer d-flex flex-row">
              <i class="bi bi-tags"></i>
              @if ($article->tags != NULL)
              <ul class="tags">
                <li><a href="{{ route('tag.detail', $article->tags->slug) }}" class="ms-2">{{ $article->tags->name }}</a></li>
              </ul>
              @else
              -
              @endif
            </div>


            @if (Auth::check())
            <div id="bookmarkListModal-{{ $article->id }}" class="modal" tabindex="-1" role="dialog">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="bookmarkListModalLabel">Select or Create List </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="background-color: rgb(214, 72, 72);">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    @if ($yourList->isEmpty())
                    <p>You don't have any list.</p>
                    @else
                    @php
                    $uniqueLists = $yourList->unique('add_id');
                    @endphp
                    @foreach ($uniqueLists as $list)
                    <div class="card mb-2">
                      <form id="bookmarkListAdd" action="{{ route('bookmark.add') }}" method="POST">
                        @csrf
                        <div class="card-body d-flex justify-content-between">
                          {{ $list->name }}
                          <input type="hidden" id="list_id" name="add_id" value="{{ $list->add_id }}">
                          <input type="hidden" id="listname" name="name" value="{{ $list->name }}">
                          <input type="hidden" id="listDescription" name="description" value="{{ $list->description }}">
                          <input type="hidden" id="article_id" name="article_id" value="{{ $article->id }}">
                          <input type="hidden" name="visibility" value="{{ $list->visibility }}">
                          @php
                          $isArticleInList = $article->articleCheckList()->where('add_id', $list->add_id)->exists();
                          @endphp

                          @if ($isArticleInList)
                          <button type="submit" class="btn btn-danger mt-2">Delete</button>
                          @else
                          <button type="submit" class="btn btn-primary mt-2">Add</button>
                          @endif
                        </div>
                      </form>
                    </div>
                    @endforeach
                    @endif

                    <hr style="border-color: black">
                    <form id="bookmarkListForm" action="{{ route('bookmark.add')}}" method="POST">
                      @csrf

                      <div class="form-group mb-3">
                        <label for="listName">List Name</label>
                        <input type="text" class="form-control" id="listName" name="name" required maxlength="60">
                      </div>
                      <div class="form-group mb-3">
                        <label for="listDescription">List Description</label>
                        <textarea class="form-control" id="listDescription" name="description" maxlength="250"></textarea>
                      </div>
                      <input type="hidden" id="article_id" name="article_id" value="{{ $article->id }}">
                      <input type="hidden" name="add_id" value="">
                      <div class="form-group mb-3">
                        <label for="listVisibility">List Visibility</label>
                        <select name="visibility" id="listVisibility" class="form-select">
                          <option value="Public">Public</option>
                          <option value="Private">Private</option>
                        </select>
                      </div>
                      <button type="submit" class="btn btn-primary mt-2">Add to List</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            @endif

          </article><!-- End blog entry -->

          <div class="blog-comments">
            @if (session('message'))
            <div class="alert alert-success" role="alert">
              {{ session('message') }}
            </div>
            @endif
            <div class="reply-form">
              <h4>Leave a Reply</h4>
              @if (Auth::check())
              <form action="{{ route('komentar.store') }}" method="POST" id="commentForm">
                @csrf
                <div class="row">
                  <div class="col form-group">
                    <textarea name="content" class="form-control" id="content" placeholder="Your Comment" required></textarea>
                  </div>
                </div>
                <input type="hidden" name="status" value="Published">
                <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                <input type="hidden" name="article_id" value="{{ $article->id }}">
                <button type="submit" class="btn btn-primary" id="postCommentBtn">Post Comment</button>
              </form>
              @else
              <a href="/signin">
                <div class="row">
                  <div class="col form-group">
                    <p name="comment" class="form-control" placeholder="Your Comment">What Are Your Thoughts?</p>
                  </div>
                </div>
              </a>
              @endif
            </div>
            @if ($publishedComments->count() > 0)
            @php
            function countReplies($comment) {
            $count = 0;
            foreach ($comment->replies as $reply) {
            $count += countReplies($reply);
            }
            return $count;
            }

            $totalComments = $publishedComments->count();
            $totalReplies = 0;
            foreach ($publishedComments as $comment) {
            $totalReplies += countReplies($comment);
            }
            $totalCommentsWithReplies = $totalComments + $totalReplies;
            @endphp
            <h4 class="comments-count mt-3">{{ $totalCommentsWithReplies }} Comments</h4>
            @foreach ($article->comments->where('status', '!=', 'Rejected') as $data)

            {{-- Level 1 --}}
            <div id="comment-2" class="comment">
              <div class="d-flex">
                @if ($data->user->image != NULL)
                <div class="comment-img"><img src="{{ asset('storage/photo/'.$data->user->image)}}" alt=""></div>
                @else
                <div class="comment-img"><img src="/images/logo-user.png" alt=""></div>
                @endif
                <div>
                  <h5><a href="">{{ $data->user->name }}</a>
                    @if (Auth::check())
                    <a style="cursor: pointer;" class="reply reply-button" data-comment-id="{{ $data->id }}"><i class="bi bi-reply-fill"></i>
                      Reply</a>
                    @if (Auth::user()->id == $data->user->id)
                    &nbsp;&nbsp;<a style="cursor: pointer;" class="reply edit-button" data-comment-id="{{ $data->id }}">Edit</a>
                    @else
                    @endif
                    @if (Auth::user()->id == $data->user->id)
                    &nbsp;&nbsp;<form class="d-inline" action="{{ route('komentar.destroy', $data->id) }}" method="POST" id="deleteForm{{ $data->id }}">
                      @csrf
                      @method('delete')
                      <a type="button" class="btn-sm btn-danger delete-button" onclick="deleteConfirmation({{ $data->id }})">Delete</a>
                    </form>
                    @else
                    @endif
                    @else
                    @endif
                    @php
                    $clapCommentArticle = DB::table('clap_comment_articles')
                    ->where('comment_article_id', $data->id)
                    ->count();
                    $userClap = Auth::check() ? $data->claps->where('user_id', Auth::user()->id)->first() : null;
                    $clapCount = $data->claps->count();
                    @endphp
                    <a href="/clap-comment/{{ $data->id }}" class="{{ $userClap ? ' text-primary' : '' }}">&nbsp;&nbsp;<i class="fa fa-hands-clapping"></i>&nbsp;{{ $clapCommentArticle }} Clap</a>
                  </h5>
                  <time datetime="{{ $data->created_at }}">{{ $data->created_at->diffForHumans() }}</time>
                  <p>
                    {{ htmlspecialchars($data->content) }}
                  </p>
                </div>
              </div>
              <!-- Form Reply -->
              <div class="reply-form" id="reply-form-{{ $data->id }}" style="display: none;">
                <form action="{{ route('komentar.store') }}" method="POST" id="reply-form-reply" onsubmit="submitReplyForm(event, {{ $data->id }})">
                  @csrf
                  @if (Auth::check())
                  <input type="hidden" name="parent_id" value="{{ $data->id }}">
                  <input type="hidden" name="status" value="Published">
                  <input type="hidden" name="article_id" value="{{ $article->id }}">
                  <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                  @else
                  @endif
                  <div class="row">
                    <div class="col form-group">
                      <textarea name="content" id="content" class="form-control" placeholder="Your Reply" required></textarea>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-primary" id="post-reply-btn-{{ $data->id }}">Post Reply</button>
                  <a style="cursor: pointer; margin-left: 10px" data-comment-id="{{ $data->id }}" class="reply-button text-secondary">Cancel</a>
                </form>
              </div>
              {{-- Form Edit --}}
              <div class="reply-form" id="edit-form-{{ $data->id }}" style="display: none;">
                <form action="{{ route('komentar.update', $data->id) }}" method="POST">
                  @csrf
                  @method('PUT')
                  {{-- <input type="hidden" name="parent_id" value="{{ $data->id }}">
                  <input type="hidden" name="status" value="Published">
                  <input type="hidden" name="article_id" value="{{ $article->id }}">
                  <input type="hidden" name="user_id" value="{{ Auth::user()->id }}"> --}}
                  <div class="row">
                    <div class="col form-group">
                      <textarea name="content" id="content" class="form-control" required>{{  htmlspecialchars($data->content)  }}</textarea>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-primary">Update Comment</button>
                  <a style="cursor: pointer; margin-left: 10px" data-comment-id="{{ $data->id }}" class="edit-button text-secondary">Cancel</a>
                </form>
              </div>


              {{-- Level 2 --}}
              @if ($data && count($data->replies) > 0)
              @foreach ($data->replies as $data)
              <div id="comment-reply-1" class="comment comment-reply">
                <div class="d-flex">
                  @if ($data->user->image != NULL)
                  <div class="comment-img"><img src="{{ asset('storage/photo/'.$data->user->image)}}" alt=""></div>
                  @else
                  <div class="comment-img"><img src="/images/logo-user.png" alt=""></div>
                  @endif
                  <div>
                    <h5><a href="">{{ $data->user->name }}</a>
                      @if (Auth::check())
                      <a style="cursor: pointer;" class="reply reply-button" data-comment-id="{{ $data->id }}"><i class="bi bi-reply-fill"></i>
                        Reply</a>
                      @if (Auth::user()->id == $data->user->id)
                      &nbsp;&nbsp;<a style="cursor: pointer;" class="reply edit-button" data-comment-id="{{ $data->id }}">Edit</a>
                      @else
                      @endif
                      @if (Auth::user()->id == $data->user->id)
                      &nbsp;&nbsp;<form class="d-inline" action="{{ route('komentar.destroy', $data->id) }}" method="POST" id="deleteForm{{ $data->id }}">
                        @csrf
                        @method('delete')
                        <a type="button" class="btn-sm btn-danger delete-button" onclick="deleteConfirmation({{ $data->id }})">Delete</a>
                      </form>
                      @else
                      @endif
                      @else
                      @endif
                      @php
                      $clapCommentArticle = DB::table('clap_comment_articles')
                      ->where('comment_article_id', $data->id)
                      ->count();
                      $userClap = Auth::check() ? $data->claps->where('user_id', Auth::user()->id)->first() : null;
                      $clapCount = $data->claps->count();
                      @endphp
                      <a href="/clap-comment/{{ $data->id }}" class="{{ $userClap ? ' text-primary' : '' }}">&nbsp;&nbsp;<i class="fa fa-hands-clapping"></i>&nbsp;{{ $clapCommentArticle }} Clap</a>
                    </h5>
                    <time datetime="{{ $data->created_at }}">{{ $data->created_at->diffForHumans() }}</time>
                    <p>
                      {{ htmlspecialchars($data->content) }}
                    </p>
                  </div>
                </div>
                <!-- Form Reply -->
                <div class="reply-form" id="reply-form-{{ $data->id }}" style="display: none;">
                  <form action="{{ route('komentar.store') }}" method="POST" id="reply-form-reply" onsubmit="submitReplyForm(event, {{ $data->id }})">
                    @csrf
                    @if (Auth::check())
                    <input type="hidden" name="parent_id" value="{{ $data->id }}">
                    <input type="hidden" name="status" value="Published">
                    <input type="hidden" name="article_id" value="{{ $article->id }}">
                    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                    @else
                    @endif
                    <div class="row">
                      <div class="col form-group">
                        <textarea name="content" id="content" class="form-control" placeholder="Your Reply" required></textarea>
                      </div>
                    </div>
                    <button type="submit" class="btn btn-primary" id="post-reply-btn-{{ $data->id }}">Post Reply</button>
                    <a style="cursor: pointer; margin-left: 10px" data-comment-id="{{ $data->id }}" class="reply-button text-secondary">Cancel</a>
                  </form>
                </div>
                {{-- Form Edit --}}
                <div class="reply-form" id="edit-form-{{ $data->id }}" style="display: none;">
                  <form action="{{ route('komentar.update', $data->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    {{-- <input type="hidden" name="parent_id" value="{{ $data->id }}">
                    <input type="hidden" name="status" value="Published">
                    <input type="hidden" name="article_id" value="{{ $article->id }}">
                    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}"> --}}
                    <div class="row">
                      <div class="col form-group">
                        <textarea name="content" id="content" class="form-control" required>{{  htmlspecialchars($data->content)  }}</textarea>
                      </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Comment</button>
                    <a style="cursor: pointer; margin-left: 10px" data-comment-id="{{ $data->id }}" class="edit-button text-secondary">Cancel</a>
                  </form>
                </div>


                {{-- Level 3 --}}
                @if ($data && count($data->replies) > 0)
                @foreach ($data->replies as $data)
                <div id="comment-reply-2" class="comment comment-reply">
                  <div class="d-flex">
                    @if ($data->user->image != NULL)
                    <div class="comment-img"><img src="{{ asset('storage/photo/'.$data->user->image)}}" alt=""></div>
                    @else
                    <div class="comment-img"><img src="/images/logo-user.png" alt=""></div>
                    @endif
                    <div>
                      <h5><a href="">{{ $data->user->name }}</a>
                        @if (Auth::check())
                        <a style="cursor: pointer;" class="reply reply-button" data-comment-id="{{ $data->id }}"><i class="bi bi-reply-fill"></i>
                          Reply</a>
                        @if (Auth::user()->id == $data->user->id)
                        &nbsp;&nbsp;<a style="cursor: pointer;" class="reply edit-button" data-comment-id="{{ $data->id }}">Edit</a>
                        @else
                        @endif
                        @if (Auth::user()->id == $data->user->id)
                        &nbsp;&nbsp;<form class="d-inline" action="{{ route('komentar.destroy', $data->id) }}" method="POST" id="deleteForm{{ $data->id }}">
                          @csrf
                          @method('delete')
                          <a type="button" class="btn-sm btn-danger delete-button" onclick="deleteConfirmation({{ $data->id }})">Delete</a>
                        </form>
                        @else
                        @endif
                        @else
                        @endif
                        @php
                        $clapCommentArticle = DB::table('clap_comment_articles')
                        ->where('comment_article_id', $data->id)
                        ->count();
                        $userClap = Auth::check() ? $data->claps->where('user_id', Auth::user()->id)->first() : null;
                        $clapCount = $data->claps->count();
                        @endphp
                        <a href="/clap-comment/{{ $data->id }}" class="{{ $userClap ? ' text-primary' : '' }}">&nbsp;&nbsp;<i class="fa fa-hands-clapping"></i>&nbsp;{{ $clapCommentArticle }} Clap</a>
                      </h5>
                      <time datetime="{{ $data->created_at }}">{{ $data->created_at->diffForHumans() }}</time>
                      <p>
                        {{ htmlspecialchars($data->content) }}
                      </p>
                    </div>
                  </div>
                  <!-- Form Reply -->
                  <div class="reply-form" id="reply-form-{{ $data->id }}" style="display: none">
                    <form action="{{ route('komentar.store') }}" method="POST" id="reply-form-reply" onsubmit="submitReplyForm(event, {{ $data->id }})">
                      @csrf
                      @if (Auth::check())
                      <input type="hidden" name="parent_id" value="{{ $data->id }}">
                      <input type="hidden" name="status" value="Published">
                      <input type="hidden" name="article_id" value="{{ $article->id }}">
                      <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                      @else
                      @endif
                      <div class="row">
                        <div class="col form-group">
                          <textarea name="content" id="content" class="form-control" placeholder="Your Reply" required></textarea>
                        </div>
                      </div>
                      <button type="submit" class="btn btn-primary" id="post-reply-btn-{{ $data->id }}">Post Reply</button>
                      <a style="cursor: pointer; margin-left: 10px" data-comment-id="{{ $data->id }}" class="reply-button text-secondary">Cancel</a>
                    </form>
                  </div>
                  {{-- Form Edit --}}
                  <div class="reply-form" id="edit-form-{{ $data->id }}" style="display: none;">
                    <form action="{{ route('komentar.update', $data->id) }}" method="POST">
                      @csrf
                      @method('PUT')
                      {{-- <input type="hidden" name="parent_id" value="{{ $data->id }}">
                      <input type="hidden" name="status" value="Published">
                      <input type="hidden" name="article_id" value="{{ $article->id }}">
                      <input type="hidden" name="user_id" value="{{ Auth::user()->id }}"> --}}
                      <div class="row">
                        <div class="col form-group">
                          <textarea name="content" id="content" class="form-control" required>{{  htmlspecialchars($data->content)  }}</textarea>
                        </div>
                      </div>
                      <button type="submit" class="btn btn-primary">Update Comment</button>
                      <a style="cursor: pointer; margin-left: 10px" data-comment-id="{{ $data->id }}" class="edit-button text-secondary">Cancel</a>
                    </form>
                  </div>


                  {{-- Level 4 --}}
                  @if ($data && count($data->replies) > 0)
                  @foreach ($data->replies as $data)
                  <div id="comment-reply-2" class="comment comment-reply">
                    <div class="d-flex">
                      @if ($data->user->image != NULL)
                      <div class="comment-img"><img src="{{ asset('storage/photo/'.$data->user->image)}}" alt=""></div>
                      @else
                      <div class="comment-img"><img src="/images/logo-user.png" alt=""></div>
                      @endif
                      <div>
                        <h5><a href="">{{ $data->user->name }}</a>
                          @if (Auth::check())
                          @if (Auth::user()->id == $data->user->id)
                          &nbsp;&nbsp;<a style="cursor: pointer;" class="reply edit-button" data-comment-id="{{ $data->id }}">Edit</a>
                          @else
                          @endif
                          @if (Auth::user()->id == $data->user->id)
                          &nbsp;&nbsp;<form class="d-inline" action="{{ route('komentar.destroy', $data->id) }}" method="POST" id="deleteForm{{ $data->id }}">
                            @csrf
                            @method('delete')
                            <a type="button" class="btn-sm btn-danger delete-button" onclick="deleteConfirmation({{ $data->id }})">Delete</a>
                          </form>
                          @else
                          @endif
                          @else
                          @endif
                          @php
                          $clapCommentArticle = DB::table('clap_comment_articles')
                          ->where('comment_article_id', $data->id)
                          ->count();
                          $userClap = Auth::check() ? $data->claps->where('user_id', Auth::user()->id)->first() : null;
                          $clapCount = $data->claps->count();
                          @endphp
                          <a href="/clap-comment/{{ $data->id }}" class="{{ $userClap ? ' text-primary' : '' }}">&nbsp;&nbsp;<i class="fa fa-hands-clapping"></i>&nbsp;{{ $clapCommentArticle }} Clap</a>
                        </h5>
                        <time datetime="2020-01-01">{{ $data->created_at->diffForHumans() }}</time>
                        <p>
                          {{ htmlspecialchars($data->content) }}
                        </p>
                      </div>
                    </div>
                  </div><!-- End comment reply #2-->
                  {{-- Form Edit --}}
                  <div class="reply-form" id="edit-form-{{ $data->id }}" style="display: none;">
                    <form action="{{ route('komentar.update', $data->id) }}" method="POST">
                      @csrf
                      @method('PUT')
                      {{-- <input type="hidden" name="parent_id" value="{{ $data->id }}">
                      <input type="hidden" name="status" value="Published">
                      <input type="hidden" name="article_id" value="{{ $article->id }}">
                      <input type="hidden" name="user_id" value="{{ Auth::user()->id }}"> --}}
                      <div class="row">
                        <div class="col form-group">
                          <textarea name="content" id="content" class="form-control" required>{{  htmlspecialchars($data->content)  }}</textarea>
                        </div>
                      </div>
                      <button type="submit" class="btn btn-primary">Update Comment</button>
                      <a style="cursor: pointer; margin-left: 10px" data-comment-id="{{ $data->id }}" class="edit-button text-secondary">Cancel</a>
                    </form>
                  </div>
                  @endforeach
                  @endif
                </div><!-- End comment reply #2-->
                @endforeach
                @endif
              </div><!-- End comment reply #1-->
              @endforeach
              @endif
            </div><!-- End comment #2-->
            @endforeach
            @else
            <h4 class="comments-count mt-3">0 Comments</h4>
            <p class="mt-5">No comments available.</p>
            @endif
          </div><!-- End blog comments -->
        </div><!-- End blog entries list -->
      </div>
    </div>
  </section><!-- End Blog Single Section -->

</main><!-- End #main -->

<!-- ======= Footer ======= -->
<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
@endsection
@push('js')
<!-- Vendor JS Files -->
{{-- <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script> --}}
<script src="/assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="/assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
<script src="/assets/vendor/swiper/swiper-bundle.min.js"></script>
<script src="/assets/vendor/php-email-form/validate.js"></script>
{{-- <script src="https://cdn.ckeditor.com/ckeditor5/37.1.0/classic/ckeditor.js"></script>
<script>
  ClassicEditor
    .create( document.querySelector( '#content' ), {
        toolbar: [ 'undo', 'redo', 'bold', 'italic' ]
    } )
    .catch( error => {
        console.log( error );
    } );
</script> --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
        $('.reply-button').click(function() {
            var commentId = $(this).data('comment-id');
            $('#reply-form-' + commentId).toggle();
        });
    });
</script>
<script>
  $(document).ready(function() {
        $('.edit-button').click(function() {
            var commentId = $(this).data('comment-id');
            $('#edit-form-' + commentId).toggle();
        });
    });
</script>
<script>
  // Fungsi untuk menampilkan SweetAlert konfirmasi
  function deleteConfirmation(articleId) {
      Swal.fire({
          title: 'Confirmation',
          text: 'Are you sure you want to delete this comment?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, delete',
          cancelButtonText: 'Cancel',
          customClass: {
              icon: 'swal2-icon swal2-warning',
              confirmButton: 'swal2-button-confirm',
          },
      }).then((result) => {
          if (result.isConfirmed) {
              // Submit form
              document.querySelector(`#deleteForm${articleId}`).submit();
          }
      });
  }
</script>
<script>
  // Disable the button and show loading state
  function disableButton() {
    var button = document.getElementById('postCommentBtn');
    button.disabled = true;
    button.innerHTML = 'Loading...';
  }
  
  // Enable the button and restore its original state
  function enableButton() {
    var button = document.getElementById('postCommentBtn');
    button.disabled = false;
    button.innerHTML = 'Post Comment';
  }
  
  // Add event listener to the form submission
  var commentForm = document.getElementById('commentForm');
  commentForm.addEventListener('submit', function(event) {
    disableButton();
    // Perform any necessary AJAX requests or other asynchronous operations
    // After the operations are complete, enable the button again using enableButton()
  });
</script>
{{-- <script>
  function submitReplyForm(event, commentId) {
    // event.preventDefault();
    disablePostReplyButton(commentId);

    // Mendapatkan data dari form
    var form = $('#reply-form-reply');
    var formData = form.serialize();

    // Mengirim data komentar melalui AJAX request
    $.ajax({
      url: form.attr('action'),
      type: form.attr('method'),
      data: formData,
      success: function(response) {
        // Tindakan setelah sukses pengiriman komentar
        enablePostReplyButton(commentId);
        // Tambahkan kode di sini untuk menangani respons setelah pengiriman komentar berhasil
      },
      error: function(xhr, status, error) {
        // Tindakan setelah gagal pengiriman komentar
        enablePostReplyButton(commentId);
        // Tambahkan kode di sini untuk menangani respons setelah pengiriman komentar gagal
      }
    });
  }

  function disablePostReplyButton(commentId) {
    var postReplyBtn = document.getElementById("post-reply-btn-" + commentId);
    postReplyBtn.disabled = true;
    postReplyBtn.innerHTML = "Loading...";
  }

  function enablePostReplyButton(commentId) {
    var postReplyBtn = document.getElementById("post-reply-btn-" + commentId);
    postReplyBtn.disabled = false;
    postReplyBtn.innerHTML = "Post Reply";
  }

  $(document).ready(function() {
    $('.reply-button').click(function() {
      var commentId = $(this).data('comment-id');
      $('#reply-form-' + commentId).toggle();
    });
  });
</script> --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous">
</script>
<script>
  function showBookmarkModal(article, event) {
            event.preventDefault();

            $('#article_id').val(article);
            $('#bookmarkListModal-' + article).modal('show');
        }
</script>
@endpush