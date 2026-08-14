{{--
    Panel sayfaları artık ayrı bir header/sidebar kurmuyor — layouts/public.blade.php'nin
    ortak kabuğunu (header + katlanır sidebar) kullanıyor. Böylece "Kitaplığım -> Favorilerim"
    gibi bir geçişte Turbo yalnızca <main> içeriğini değiştiriyor, header/sidebar sabit kalıyor
    (YouTube tarzı). @section('content')/@section('title') tanımlayan panel view'ları hiç
    değişmeden bu katmandan geçiyor.
--}}
@extends('layouts.public')
