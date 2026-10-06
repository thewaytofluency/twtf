<x-layouts.admin title="Edit Plan">
    <form method="POST" action="{{ route('admin.plans.update', $plan) }}">
        @csrf
        @method('PUT')
        @include('admin.plans._form')
    </form>
</x-layouts.admin>
