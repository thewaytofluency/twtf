<x-layouts.admin title="New Plan">
    <form method="POST" action="{{ route('admin.plans.store') }}">
        @csrf
        @include('admin.plans._form')
    </form>
</x-layouts.admin>
