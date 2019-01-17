@extends('sidebar-layout')

@section('toolbar')
    <div class="col-sm-6 col-xs-1  faded">
        @include('shelves._breadcrumbs', ['shelf' => $shelf])
    </div>
    <div class="col-sm-6 col-xs-11">
        <div class="action-buttons faded">
            @if(userCan('bookshelf-update', $shelf))
                @include('partials.auth_link')
            @endif
            @if(userCan('bookshelf-update', $shelf))
                <a href="{{ $shelf->getUrl('/edit') }}" class="text-button text-primary">@icon('edit'){{ trans('common.edit') }}</a>
            @endif
            @if(userCan('restrictions-manage', $shelf) || userCan('bookshelf-delete', $shelf))
                <div dropdown class="dropdown-container">
                    <a dropdown-toggle class="text-primary text-button">@icon('more'){{ trans('common.more') }}</a>
                    <ul>
                        @if(userCan('restrictions-manage', $shelf))
                            <li><a href="{{ $shelf->getUrl('/permissions') }}" class="text-primary">@icon('lock'){{ trans('entities.permissions') }}</a></li>
                        @endif
                        @if(userCan('bookshelf-delete', $shelf))
                            <li><a href="{{ $shelf->getUrl('/delete') }}" class="text-neg">@icon('delete'){{ trans('common.delete') }}</a></li>
                        @endif
                    </ul>
                </div>
            @endif
        </div>
    </div>
@stop

@section('sidebar')

    @if($shelf->tags->count() > 0)
        <section>
            @include('components.tag-list', ['entity' => $shelf])
        </section>
    @endif

    <div class="card entity-details">
        <h3>@icon('info') {{ trans('common.details') }}</h3>
        <div class="body text-small text-muted blended-links">
            @include('partials.entity-meta', ['entity' => $shelf])
            @if($shelf->restricted)
                <div class="active-restriction">
                    @if(userCan('restrictions-manage', $shelf))
                        <a href="{{ $shelf->getUrl('/permissions') }}">@icon('lock'){{ trans('entities.shelves_permissions_active') }}</a>
                    @else
                        @icon('lock'){{ trans('entities.shelves_permissions_active') }}
                    @endif
                </div>
            @endif
        </div>
    </div>

    @if(count($activity) > 0)
        <div class="activity card">
            <h3>@icon('time') {{ trans('entities.recent_activity') }}</h3>
            @include('partials/activity-list', ['activity' => $activity])
        </div>
    @endif
@stop

@section('body')

    <div class="container small nopad">
        <h1 class="break-text">{{$shelf->name}}</h1>
        <div class="book-content">
            <p class="text-muted">{!! nl2br(e($shelf->description)) !!}</p>
            @if(count($books) > 0)
            <div class="page-list">
                <hr>
                @foreach($books as $book)
                    @include('books/list-item', ['book' => $book])
                    <hr>
                @endforeach
            </div>
            @else
            <p>
                <hr>
                <span class="text-muted italic">{{ trans('entities.shelves_empty_contents') }}</span>
                @if(userCan('bookshelf-create', $shelf))
                    <br>
                    <a href="{{ $shelf->getUrl('/edit') }}" class="button outline bookshelf">{{ trans('entities.shelves_edit_and_assign') }}</a>
                @endif
            </p>
            @endif

    </div>

@stop




        @section('scripts')
            @parent()
            <script>
                Vue.component('multiselect', window.VueMultiselect.default)

                new Vue({
                    el: "#modal",

                    data() {
                        return {
                            users: [],
                            selected: null,
                            isLoading: false,
                            permissionName: 'shelves-view-all',
                            link: ''
                        }
                    },

                    methods: {
                        limitText(count) {
                            return `and ${count} other users`
                        },
                        asyncFind(query) {
                            this.isLoading = true

                            axios.get('/search/users', {params: {query: query, permission_name: this.permissionName}})
                                .then(({data}) => {
                                    this.users = data;
                                    this.isLoading = false;
                                })
                        },
                        clearAll() {
                            this.users = []
                        },

                        getLink(selected) {
                            let link = @json(request()->path());
                            axios.get('/search/link', {params: {user: selected.id, link: link}})
                                .then(({data}) => {
                                    console.log(data)
                                    this.link = data.link;
                                })
                        }
                    }
                })
            </script>
    @endsection

    @section('head')
        @parent()
        <!-- Remember to include jQuery :) -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
            <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

            <!-- jQuery Modal -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css"/>

            <!-- development version, includes helpful console warnings -->
            <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>

            <script src="https://unpkg.com/vue-multiselect@2.1.0"></script>
            <link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
@endsection