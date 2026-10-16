<section class="section profil decor-host">
    <x-dot color="gold" :size="56" pos="left:46%;top:60px" />
    <x-dot color="navy" :size="34" pos="left:56%;top:20px" />
    <x-dot color="gold" :size="74" pos="right:18%;top:40px" />
    <x-dot color="navy" :size="200" pos="right:-70px;top:60px" />
    <x-wave color="gold" pos="right:0;top:250px;width:42%" />

    <div class="container profil-grid">
        <div class="profil-text">
            <h2 class="profil-title">
                Halo, saya Leo. Saya yang akan <em>membantu</em> anak Anda meraih kemampuan terbaiknya.
            </h2>
            <p>
                Setiap anak punya kecepatan belajar yang berbeda. Karena itu saya mengajar pelan-pelan,
                dimulai dari rasa nyaman di air, lalu gerakan dasar, sampai anak berani dan percaya diri.
            </p>
            <p>Yang saya jaga di setiap pertemuan: aman, menyenangkan, dan terasa kemajuannya.</p>

            <dl class="profil-stats">
                <div><dt>Pemula–Mahir</dt><dd>semua level</dd></div>
                <div><dt>Dini–Remaja</dt><dd>tingkat usia</dd></div>
                <div><dt>3 wilayah</dt><dd>Surabaya Timur, pusat dan Selatan</dd></div>
            </dl>

            <p class="signature">Coach Leo Aswandany, S.Pd</p>
        </div>

        <div class="profil-photo">
            <x-dot color="blue" :size="130" pos="left:-40px;top:60px" />
            <div class="photo-arch">
                <img src="{{ asset('images/profil/leo.jpg') }}" alt="Coach Leo Aswandany, S.Pd di kolam renang">
            </div>
            <div class="photo-card">
                <small>Belajar step by step</small>
                <strong>Sesuai kemampuanmu!</strong>
            </div>
        </div>
    </div>
</section>
