<form method="POST" action="{{ route('statuses.store') }}">
    @include('shared._errors')
    {{ csrf_field() }}
    <textarea class="form-control" name="content" rows="3" placeholder="聊聊新鲜事儿...">{{ old('content') }}</textarea>
    <button class="btn btn-primary pull-right" type="submit">发布</button>
</form>