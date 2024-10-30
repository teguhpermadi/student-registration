<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Preview</title>
    {{-- <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'> --}}
    {{-- <link rel="stylesheet" href="{{ asset('css/pdf.css') }}" type="text/css"> --}}
</head>

<body>
    <img src="{{ asset('image/logo.png') }}" alt="logo" height="100" class="center-img">
    <h1 class="center">Formulir Pendaftaran Peserta Didik Baru</h1>
    <h2 class="center">MI AR RIDLO</h2>
    <h3 class="center">Tahun Pelajaran : {{$student->academicYear->year}}</h3>
    <hr>
    <table>
        <tr>
            <td colspan="3">
                <h4>Identitas Siswa</h4>
            </td>
        </tr>
        <tr>
            <td>Nama Lengkap</td>
            <td>:</td>
            <td>{{ $student->full_name }}</td>
        </tr>
        <tr>
            <td>Nama Panggilan</td>
            <td>:</td>
            <td>{{ $student->nick_name }}</td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td>
            <td>:</td>
            <td>{{ $student->gender }}</td>
        </tr>
        <tr>
            <td>Tempat Lahir</td>
            <td>:</td>
            <td>{{ $student->city_born }}</td>
        </tr>
        <tr>
            <td>Tanggal Lahir</td>
            <td>:</td>
            <td>{{ $student->birthday }}</td>
        </tr>
        <tr>
            <td>NISN</td>
            <td>:</td>
            <td>{{ $student->nisn }}</td>
        </tr>
        <tr>
            <td>NIK</td>
            <td>:</td>
            <td>{{ $student->nik }}</td>
        </tr>
        <tr>
            <td colspan="3">
                <h4>Alamat</h4>
            </td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>:</td>
            <td>{{ $student->address }}</td>
        </tr>
        <tr>
            <td>Kelurahan</td>
            <td>:</td>
            <td>{{ $student->village }}</td>
        </tr>
        <tr>
            <td>Kecamatan</td>
            <td>:</td>
            <td>{{ $student->district }}</td>
        </tr>
        <tr>
            <td>Kota</td>
            <td>:</td>
            <td>{{ $student->city }}</td>
        </tr>
        <tr>
            <td>Provinsi</td>
            <td>:</td>
            <td>{{ $student->province }}</td>
        </tr>
        <tr>
            <td>Kode Pos</td>
            <td>:</td>
            <td>{{ $student->poscode }}</td>
        </tr>
        <tr></tr>
        <tr>
            <td colspan="3">
                <h4>Identitas Ayah</h4>
            </td>
        </tr>
        <tr>
            <td>Status</td>
            <td>:</td>
            <td>{{ $student->father_status }}</td>
        </tr>
        <tr>
            <td>NIK Ayah</td>
            <td>:</td>
            <td>{{ $student->father_nik }}</td>
        </tr>
        <tr>
            <td>Nama Ayah</td>
            <td>:</td>
            <td>{{ $student->father_name }}</td>
        </tr>
        <tr>
            <td>Tempat Lahir Ayah</td>
            <td>:</td>
            <td>{{ $student->father_city_born }}</td>
        </tr>
        <tr>
            <td>Tanggal Lahir Ayah</td>
            <td>:</td>
            <td>{{ $student->father_birthday }}</td>
        </tr>
        <tr>
            <td>Agama Ayah</td>
            <td>:</td>
            <td>{{ $student->father_religon }}</td>
        </tr>
        <tr>
            <td>Pendidikan Ayah</td>
            <td>:</td>
            <td>{{ $student->father_education }}</td>
        </tr>
        <tr>
            <td>Hubungan Ayah dengan anak</td>
            <td>:</td>
            <td>{{ $student->father_relation }}</td>
        </tr>
        <tr>
            <td>Pekerjaan Ayah</td>
            <td>:</td>
            <td>{{ $student->father_job }}</td>
        </tr>
        <tr>
            <td>Penghasilan Ayah</td>
            <td>:</td>
            <td>{{ $student->father_income }}</td>
        </tr>
        <tr>
            <td>Telp Ayah</td>
            <td>:</td>
            <td>{{ $student->father_phone }}</td>
        </tr>
        <tr>
            <td colspan="3">
                <h4>Identitas Ibu</h4>
            </td>
        </tr>
        <tr>
            <td>Status</td>
            <td>:</td>
            <td>{{ $student->mother_status }}</td>
        </tr>
        <tr>
            <td>NIK Ibu</td>
            <td>:</td>
            <td>{{ $student->mother_nik }}</td>
        </tr>
        <tr>
            <td>Nama Ibu</td>
            <td>:</td>
            <td>{{ $student->mother_name }}</td>
        </tr>
        <tr>
            <td>Tempat Lahir Ibu</td>
            <td>:</td>
            <td>{{ $student->mother_city_born }}</td>
        </tr>
        <tr>
            <td>Tanggal Lahir Ibu</td>
            <td>:</td>
            <td>{{ $student->mother_birthday }}</td>
        </tr>
        <tr>
            <td>Agama Ibu</td>
            <td>:</td>
            <td>{{ $student->mother_religon }}</td>
        </tr>
        <tr>
            <td>Pendidikan Ibu</td>
            <td>:</td>
            <td>{{ $student->mother_education }}</td>
        </tr>
        <tr>
            <td>Hubungan Ibu dengan anak</td>
            <td>:</td>
            <td>{{ $student->mother_relation }}</td>
        </tr>
        <tr>
            <td>Pekerjaan Ibu</td>
            <td>:</td>
            <td>{{ $student->mother_job }}</td>
        </tr>
        <tr>
            <td>Penghasilan Ibu</td>
            <td>:</td>
            <td>{{ $student->mother_income }}</td>
        </tr>
        <tr>
            <td>Telp Ibu</td>
            <td>:</td>
            <td>{{ $student->mother_phone }}</td>
        </tr>
    </table>

    <div class="center">
        <p>Malang, {{ $student->created_at->format('j F Y') }}</p>
        <img src="{{ asset('storage/' . $student->ttd) }}" alt="Signature" height="40">
        <p>{{ $student->ttd_name }}</p>
    </div>
</body>

</html>
