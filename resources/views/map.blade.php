<div id="map" style="height: 400px;"></div>
<p id="location-name" style="margin-top: 10px; font-weight: bold;"></p>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const map = L.map('map').setView([34.8193798100381, 36.117517379146804], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            let marker;

            const latitudeInput = document.getElementById('data.latitude');
            const longitudeInput = document.getElementById('data.longitude');
            const cityInput = document.getElementById('data.city');
            const regionInput = document.getElementById('data.region');
            const countryInput = document.getElementById('data.country');
            const locationNameElement = document.getElementById('location-name');

            // ✅ عرض الموقع إن وجدت الإحداثيات
            if (latitudeInput?.value && longitudeInput?.value) {
                const lat = parseFloat(latitudeInput.value);
                const lng = parseFloat(longitudeInput.value);

                if (!isNaN(lat) && !isNaN(lng)) {
                    marker = L.marker([lat, lng]).addTo(map);
                    map.setView([lat, lng], 13);
                    fetchLocationName(lat, lng);
                }
            }

            // ✅ إحضار اسم الموقع
            function fetchLocationName(lat, lng) {
                fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
                    .then(response => response.json())
                    .then(data => {
                        const address = data.address;
                        const city = address.city || address.town || address.village || '';
                        const region = address.state || '';
                        const country = address.country || '';

                        const fullLocation = [city, region, country].filter(Boolean).join(', ');
                        if (locationNameElement) {
                            locationNameElement.textContent = fullLocation;
                        }

                        if (cityInput) cityInput.value = city;
                        if (regionInput) regionInput.value = region;
                        if (countryInput) countryInput.value = country;

                        cityInput?.dispatchEvent(new Event('input'));
                        regionInput?.dispatchEvent(new Event('input'));
                        countryInput?.dispatchEvent(new Event('input'));
                    })
                    .catch(error => {
                        console.error('فشل في جلب اسم الموقع:', error);
                    });
            }

            // ✅ عند الضغط على الخريطة
            function onMapClick(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;

                if (marker) {
                    marker.setLatLng(e.latlng);
                } else {
                    marker = L.marker(e.latlng).addTo(map);
                }

                if (latitudeInput) {
                    latitudeInput.value = lat;
                    latitudeInput.dispatchEvent(new Event('input'));
                }

                if (longitudeInput) {
                    longitudeInput.value = lng;
                    longitudeInput.dispatchEvent(new Event('input'));
                }

                fetchLocationName(lat, lng);
            }

            map.on('click', onMapClick);
        });
    </script>
    
@endpush
