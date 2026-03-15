<style>
    .search-box {
        position: relative;
        max-width: 300px;
    }

    .search-box .form-control {
        padding-left: 2rem;
    }

    .search-box .search-icon {
        position: absolute;
        top: 50%;
        left: 10px;
        transform: translateY(-50%);
        color: #999;
    }

    .search-box .search-icon svg {
        width: 14px;
        height: 14px;
    }
</style>

<x-layout>
    <x-breadcrumb :title="'Production report'" />
    <div class="row">
        <div class="col-sm-12">

            {{-- Filter Card --}}
            <div class="card">
                <div class="card-header">
                    <h5>Search report</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('production_reports.index') }}">
                        <div class="row">
                            <div class="col-3 mb-3">
                                <input type="date" class="form-control" name="start_date"
                                    value="{{ request('start_date') }}">
                                <small class="form-text text-muted">Start date</small>
                            </div>
                            <div class="col-3 mb-3">
                                <input type="date" class="form-control" name="end_date"
                                    value="{{ request('end_date') }}">
                                <small class="form-text text-muted">End date</small>
                            </div>
                            <div class="col-3 mb-3">
                                <input type="time" class="form-control" name="start_time"
                                    value="{{ request('start_time') }}">
                                <small class="form-text text-muted">Start time</small>
                            </div>
                            <div class="col-3 mb-3">
                                <input type="time" class="form-control" name="end_time"
                                    value="{{ request('end_time') }}">
                                <small class="form-text text-muted">End time</small>
                            </div>
                            <div class="col-6 mb-3">
                                <select id="select-kategori" name="kategori" class="form-select">
                                    <option value="">-- Semua Kategori --</option>
                                    <option value="wip" @selected(request('kategori') === 'wip')>WIP</option>
                                    <option value="finish_good" @selected(request('kategori') === 'finish_good')>Finish Good</option>
                                </select>
                                <small class="form-text text-muted">Kategori</small>
                            </div>
                            <div class="col-6 mb-3">
                                <select id="select-workPlaces" name="work_place_id" class="form-select">
                                    <option value="">-- Semua Lokasi --</option>
                                    @foreach ($workPlaces as $workPlace)
                                        <option value="{{ $workPlace->id }}" @selected(request('work_place_id') == $workPlace->id)>
                                            {{ $workPlace->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Lokasi</small>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Search</button>
                        <a href="{{ route('production_reports.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                    </form>
                </div>
            </div>

            {{-- Table Card --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">All data</h5>
                    @if ($operators !== null)
                        <a href="{{ route('production_reports.export') }}?{{ http_build_query(request()->all()) }}"
                            class="btn btn-success btn-sm">
                            <i class="ti ti-file-spreadsheet me-1"></i> Export Excel
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @include('production_reports._table')
                </div>
            </div>

        </div>
    </div>
</x-layout>

<script>
    new TomSelect('#select-kategori', {
        allowEmptyOption: true
    });
    new TomSelect('#select-workPlaces', {
        allowEmptyOption: true
    });
</script>
