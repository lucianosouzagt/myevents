@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
    <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Criar Novo Evento</h2>
    
    <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-5">
            <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Título do Evento</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
        </div>
        
        <div class="mb-5">
            <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Descrição</label>
            <textarea name="description" id="description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ old('description') }}</textarea>
        </div>

        <div class="mb-5">
            <label for="whatsapp_message_template" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Mensagem Personalizada WhatsApp (Opcional)</label>
            <textarea name="whatsapp_message_template" id="whatsapp_message_template" rows="3" placeholder="Olá @{{guest_name}}! Você foi convidado para @{{event_title}}..." class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ old('whatsapp_message_template') }}</textarea>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Variáveis disponíveis: @{{guest_name}}, @{{event_title}}, @{{event_date}}, @{{event_location}}, @{{rsvp_link}}</p>
        </div>

        <div class="mb-5">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="invitation_image">Imagem do Convite</label>
            <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="invitation_image" name="invitation_image" type="file" accept="image/png, image/jpeg, image/gif" onchange="previewImage(event)">
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">JPG, PNG ou GIF (Max. 5MB).</p>
            <div id="image_preview" class="mt-4 hidden">
                <p class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Pré-visualização:</p>
                <img id="preview_img" src="#" alt="Pré-visualização da imagem" class="max-w-full h-auto max-h-[300px] rounded-lg border border-gray-200 dark:border-gray-600">
            </div>
        </div>

        <div class="mb-5">
            <div class="flex items-center">
                <input id="has_end_time" name="has_end_time" type="checkbox" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" {{ old('has_end_time') ? 'checked' : '' }}>
                <label for="has_end_time" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Evento com inicio e fim</label>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
            <div>
                <label for="start_time" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Início</label>
                <input type="datetime-local" name="start_time" id="start_time" value="{{ old('start_time') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
            </div>
            
            <div class="{{ old('has_end_time') ? '' : 'hidden' }}" id="end_time_container">
                <label for="end_time" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fim</label>
                <input type="datetime-local" name="end_time" id="end_time" value="{{ old('end_time') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
        </div>

        <div class="mb-5">
            <label for="rsvp_deadline" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Data Limite para Confirmação (RSVP) - Opcional</label>
            <input type="datetime-local" name="rsvp_deadline" id="rsvp_deadline" value="{{ old('rsvp_deadline') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Se definido, os convidados não poderão alterar sua resposta após esta data.</p>
        </div>

        <div class="mb-5">
            <label for="location" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Local</label>
            <input type="text" name="location" id="location" value="{{ old('location') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
        </div>

        <div class="mb-5">
            <label for="location_url" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">URL do Local (Opcional)</label>
            <input type="url" name="location_url" id="location_url" value="{{ old('location_url') }}" placeholder="https://maps.google.com/..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Link direto para o mapa (Google Maps, Waze, etc).</p>
        </div>

        <div class="mb-5 hidden">
            <label for="google_maps_link" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Link do Google Maps (Opcional)</label>
            <input type="url" name="google_maps_link" id="google_maps_link" value="{{ old('google_maps_link') }}" placeholder="https://maps.google.com/..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>

        <div class="mb-5">
            <label for="capacity" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Capacidade Máxima</label>
            <input type="number" name="capacity" id="capacity" min="1" value="{{ old('capacity') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Criar Evento</button>
            <a href="{{ route('events.my') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Cancelar</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hasEndTimeCheckbox = document.getElementById('has_end_time');
        const endTimeContainer = document.getElementById('end_time_container');
        const endTimeInput = document.getElementById('end_time');

        function toggleEndTime() {
            if (hasEndTimeCheckbox.checked) {
                endTimeContainer.classList.remove('hidden');
                endTimeInput.required = true;
            } else {
                endTimeContainer.classList.add('hidden');
                endTimeInput.required = false;
                endTimeInput.value = ''; // Limpar valor
            }
        }

        // Event Listener
        hasEndTimeCheckbox.addEventListener('change', toggleEndTime);

        // Initial check (in case of old input or edit)
        toggleEndTime();
    });

    function previewImage(event) {
        const input = event.target;
        const previewDiv = document.getElementById('image_preview');
        const previewImg = document.getElementById('preview_img');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewDiv.classList.remove('hidden');
            }

            reader.readAsDataURL(input.files[0]);
        } else {
            previewImg.src = '#';
            previewDiv.classList.add('hidden');
        }
    }
</script>
@endsection
