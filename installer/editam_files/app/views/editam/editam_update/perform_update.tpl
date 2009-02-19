<h2>_{Editam Update completed}</h2>

<p>
_{You are now using the latest Version of Editam (<strong>%update-to).}
</p>

<div id="modifications">
    <p>_{Modifications performed by this update}</p>
    <ul>
    {loop EditamUpdate.modifications}
      <li>{modification}</li>
    {end}
    </ul>
</div>