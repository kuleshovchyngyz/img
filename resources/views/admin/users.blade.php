@extends('layouts.app')
@section('content')

  <div class="container">
    <table class="table">
      <thead class="thead-dark">
      <tr>
        <th scope="col">#</th>
        <th scope="col">Name</th>
        <th scope="col">Email</th>
        <th scope="col">last login</th>
        <th scope="col">Number of pics</th>
      </tr>
      </thead>
      <tbody>
      @foreach($users as $key => $user)
        <tr>
          <th scope="row">{{ $key+1 }}</th>
          <td>{{ $user->name }}</td>
          <td>{{ $user->email }}</td>
          <td>{{ $user->login }}</td>
          <td>{{ $user->numberOfImages }}</td>
        </tr>
      @endforeach

      </tbody>
    </table>


  </div>

@endsection