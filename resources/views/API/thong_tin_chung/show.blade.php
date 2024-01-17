<html>

<head>
    <title>{{ $thong_tin_chung->tieu_de }}</title>
    <style>
        h1 {
            color: green;
            text-align: center;
        }
    </style>
</head>

<body>
    <h1>{{ $thong_tin_chung->tieu_de }}</h1>
    <p>{!!$thong_tin_chung->noi_dung!!}</p>
</body>

</html>