{{-- resources/views/forms/components/map-picker-field.blade.php --}}
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :state-path="$getStatePath()"
>
    <div
        x-data="{
            map: null,
            marker: null,
            latitude: @entangle($getStatePath() . '.latitude'),
            longitude: @entangle($getStatePath() . '.longitude'),
            init() {
                // Default to Damascus if no initial coordinates
                const defaultLat = this.latitude || 33.51304;
                const defaultLng = this.longitude || 36.29128;

                this.map = L.map($el.querySelector('#map-{{ $getId() }}')).setView([defaultLat, defaultLng], 13);

                L.tileLayer('https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}.png?apiKey={{ config('services.geoapify.key') }}', {
                    attribution: 'Powered by <a href="https://www.geoapify.com/" target="_blank">Geoapify</a> | &copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors',
                    maxZoom: 20,
                    id: 'osm-bright',
                }).addTo(this.map);

                this.marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(this.map);

                this.marker.on('dragend', (event) => {
                    const latLng = event.target.getLatLng();
                    this.latitude = latLng.lat;
                    this.longitude = latLng.lng;
                });

                this.map.on('click', (e) => {
                    this.latitude = e.latlng.lat;
                    this.longitude = e.latlng.lng;
                    this.marker.setLatLng(e.latlng);
                });

                // Watch for Livewire updates to latitude/longitude
                this.$watch('latitude', (newLat) => {
                    if (this.marker && newLat !== this.marker.getLatLng().lat) {
                        this.marker.setLatLng([newLat, this.longitude]);
                        this.map.setView([newLat, this.longitude]);
                    }
                });
                this.$watch('longitude', (newLng) => {
                    if (this.marker && newLng !== this.marker.getLatLng().lng) {
                        this.marker.setLatLng([this.latitude, newLng]);
                        this.map.setView([this.latitude, newLng]);
                    }
                });
            }
        }"
        wire:ignore
        class="relative"
    >
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
              integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
              crossorigin=""/>
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
                integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
                crossorigin=""></script>

        <div id="map-{{ $getId() }}" style="height: 400px; width: 100%;"></div>
    </div>
</x-dynamic-component>
