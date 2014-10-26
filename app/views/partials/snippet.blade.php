<div class="row snippet-box">
  @if($img == true)
  <div class="col-md-2 col-sm-3 col-xs-3">
    <a href="{{ URL::route('snippet.show', $snippet['id']) }}">
      <img src="{{ $snippet['user']->avatar_url }}" class="img-responsive">
    </a>
  </div>
  <div class="col-md-10 col-xs-9">
  @else
  <div class="col-md-10 col-xs-12">
  @endif
    <h3 class="col-xs-12"><a href="{{ URL::route('snippet.show', $snippet['id']) }}">{{{ $snippet['title'] }}}</a></h3>
    <p class="text-muted small col-xs-6">
      {{ $snippet['created_at']->diffForHumans() }}<br>
      by <a href="{{ URL::route('user.show', $snippet['user']->id)  }}">{{ $snippet['user']->name }}</a>
      @if (Auth::check() && $snippet['user_id'] === Auth::user()->id)
        <a href="{{ URL::route('snippet.edit', $snippet['id']) }}""button">[edit]</a>
      @endif
    </p>
    <p class="text-muted small col-xs-6">
      <i class="fa fa-fire"></i> &nbsp;{{ $snippet->getScore() }} {{ Str::plural('Point', $snippet->getScore()) }}<br>
      <i class="fa fa-comments-o"></i> {{ count($snippet->comments) }} {{ Str::plural('Comment', count($snippet->comments)) }}
    </p>
  </div>
</div>
