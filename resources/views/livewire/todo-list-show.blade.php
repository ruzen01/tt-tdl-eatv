<div>
    <div class="container">
        <h2>{{ $todoList->name }}</h2>
        <p>Status: {{ $todoList->status }}</p>

        <h4>Tasks:</h4>
        <ul class="list-group">
            @forelse($todoList->tasks as $task)
            @if($task->status === 'Public')
            <li class="list-group-item">
                {{ $task->name }} - {{ $task->status }} - {{ $task->progress }}
            </li>
            @endif
            @empty
            <li class="list-group-item">
                No tasks in this list yet
            </li>
            @endforelse
        </ul>

        <div class="mt-3">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                Back to Dashboard
            </a>
        </div>
    </div>
</div>