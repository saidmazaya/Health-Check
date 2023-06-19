@extends('layout.home')

@section('title', 'Informasi')

@section('konten')

<!-- ======= Team Section ======= -->
<section id="services" class="services section-bg">
  <div class="container" data-aos="fade-up">

    <div class="section-title">
      <h2>Informasi</h2>
    </div>

    <div class="row">

      @foreach ($article as $data)
      <div class="col-lg-4 col-md-6 align-items-stretch mt-4 mt-0" data-aos="zoom-in" data-aos-delay="200">
        <div class="icon-box iconbox-blue ">
          <div class="icon">
            <svg width="100" height="100" viewBox="0 0 600 600" xmlns="http://www.w3.org/2000/svg">
              <path stroke="none" stroke-width="0" fill="#f5f5f5"
                d="M300,582.0697525312426C382.5290701553225,586.8405444964366,449.9789794690241,525.3245884688669,502.5850820975895,461.55621195738473C556.606425686781,396.0723002908107,615.8543463187945,314.28637112970534,586.6730223649479,234.56875336149918C558.9533121215079,158.8439757836574,454.9685369536778,164.00468322053177,381.49747125262974,130.76875717737553C312.15926192815925,99.40240125094834,248.97055460311594,18.661163978235184,179.8680185752513,50.54337015887873C110.5421016452524,82.52863877960104,119.82277516462835,180.83849132639028,109.12597500060166,256.43424936330496C100.08760227029461,320.3096726198365,92.17705696193138,384.0621239912766,124.79988738764834,439.7174275375508C164.83382741302287,508.01625554203684,220.96474134820875,577.5009287672846,300,582.0697525312426">
              </path>
            </svg>
            <i class="bi bi-info-lg"></i>
          </div>
          <h4><a href="{{ route('informasi.show', $data->slug) }}">{{ $data->title }}</a></h4>
          <p>{{ $data->description }}</p>
        </div>
      </div>
      @endforeach
      <div class="mt-3">
        {{ $article->links() }}
      </div>
    </div>

  </div>
</section>


@endsection