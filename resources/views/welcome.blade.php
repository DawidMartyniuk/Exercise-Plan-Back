<form method="POST" action="{{ route('password.reset') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="email" name="email" value="{{ $email }}" readonly>
    <input type="password" name="password" placeholder="Nowe hasło">
    <input type="password" name="password_confirmation" placeholder="Potwierdź hasło">
    <button type="submit">Resetuj hasło</button>
</form>
