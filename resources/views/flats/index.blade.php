@extends('layouts.app')

@section('content')

  <div id="index-container" class="container mt-3">

    <div class="row ">
      <div class="col-md-12 d-flex justify-content-between align-items-center">
        <h1 class="pt-4">Benvenuto, {{ $firstname }}</h1>
        <div class=" ">
          <a href="{{ route('flats.create') }}" class="btn btn-primary">Affitta un nuovo appartamento</a>
        </div>
      </div>
    </div>

    <hr>

    {{-- Appartamenti in affitto sponsorizzati --}}
    @isset($flatsSponsored)

        @foreach ($flatsSponsored as $flat)

          @if ($loop -> first)
            <h2>APPARTAMENTI SPONSORIZZATI</h2>
          @endif

          <div class="row m-3 table-warning">

            <div class="index-img col-xs-12 col-md-12 col-lg-5 col-xl-3 p-2">
              <img height="150px" src="{{ asset($flat -> photos() -> first() -> path) }}" class="rounded img-fluid" alt="Flat Image">
            </div>

            <div class="col-xs-12 col-md-12 col-lg-7 col-xl-9 p-2">
              <div class="title-text">
                <h3 class="mb-0">{{ $flat -> title }}</h3>
                <div class="small">Creato il: {{ $flat -> created_at }}</div>
                <div class="small">Sponsorizzato il: {{ $flat -> active_sponsor() -> first() -> pivot -> created_at }}</div>
                <div class="small">Fine sponsorizzazione: {{ $flat -> active_sponsor() -> first() -> pivot -> expires_at }}</div>
              </div>
              <p class="text-muted mt-2">{{ $flat -> desc }}</p>
              <div class="index-button">
                  <a href="{{ route('flats.show', $flat -> id) }}" class="btn btn-primary"> Visualizza</a>
                  <a href="{{ route('flats.edit', $flat -> id) }}" class="btn"><i class="fas fa-edit"></i></a>
                  <a href="{{ route('flats.stats', $flat -> id) }}" class="btn btn-primary"><i class="fas fa-chart-line"></i></a>
                  <a href="{{ route('flats.messages', $flat -> id) }}" class="btn btn-primary"><i class="far fa-envelope"></i></a>
                  <a href="{{ route('flats.deactivate', $flat -> id) }}" class="btn float-right">Disattiva</a>
                </div>
              </div>

            </div>

            @endforeach

      @endisset

    {{-- Appartamenti in affitto --}}
    @isset($flats)

        @foreach ($flats as $flat)
          {{-- {{ dd($flat)}} --}}
          @if ($loop -> first)
            <h2>APPARTAMENTI IN AFFITTO</h2>
          @endif

          <div class="row m-3">

            <div class="index-img col-xs-12 col-md-12 col-lg-5 col-xl-3 p-2">
              <img src="{{ asset($flat -> photos() -> first() -> path) }}" class="rounded img-fluid" alt="Flat Image">
            </div>

            <div class="col-xs-12 col-md-12 col-lg-7 col-xl-9 p-2">
              <div class="title-text">
                <h3 class="mb-0">{{ $flat -> title }}</h3>
                <div class="small">Creato il: {{ $flat -> created_at }}</div>
              </div>
              <p class="text-muted mt-2">{{ $flat -> desc }}</p>
              <div class="index-button">
                  <a href="{{ route('flats.show', $flat -> id) }}" class="btn btn-primary"> Visualizza</a>
                  <a href="{{ route('flats.edit', $flat -> id) }}" class="btn "><i class="fas fa-edit"></i></a>
                  <a href="{{ route('flats.stats', $flat -> id) }}" class="btn btn-primary"><i class="fas fa-chart-line"></i></a>
                  <a href="{{ route('flats.messages', $flat -> id) }}" class="btn btn-primary"><i class="far fa-envelope"></i></a>
                  <a href="{{ route('flats.sponsor.create', $flat -> id) }}" class="btn btn-primary">Sponsorizza</a>
                  <a href="{{ route('flats.deactivate', $flat -> id) }}" class="btn float-right">Disattiva</a>
                </div>
              </div>

            </div>

            @endforeach

      @endisset

      {{-- Appartamenti disattivati --}}
      @isset($flatsTrashed)

          @foreach ($flatsTrashed as $flat)

            @if ($loop -> first)
                <h2>APPARTAMENTI DISATTIVATI</h2>
            @endif

              <div class="row m-3">

                <div class="index-img col-xs-12 col-md-3 p-2 opacity">
                  <img src="{{ asset($flat -> photos() -> first() -> path) }}" class="rounded img-fluid" alt="Flat Image">
                </div>

                <div class="col-xs-12 col-md-9 p-2">
                  <div class="title-text opacity">
                    <h3 class="mb-0">{{ $flat -> title }}</h3>
                    <div class="small">Creato il: {{ $flat -> created_at }}</div>
                  </div>
                  <p class="text-muted opacity mt-2">{{ $flat -> desc }}</p>
                  <div class="index-button">
                    <a href="{{ route('flats.activate', $flat -> id) }}" class="btn btn-primary">Attiva</a>
                    <span class="text-danger float-right"><strong>Questo appartamento è disattivato</strong></span>
                  </div>
                </div>

              </div>

            @endforeach

        @endisset

    </div>

@endsection
