<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Presupost d'Esdeveniment - EventAI</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.5; }
        .header { text-align: center; border-bottom: 2px solid #6366f1; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 28px; font-weight: bold; color: #6366f1; }
        .logo span { color: #333; }
        .title { font-size: 22px; margin-top: 10px; text-transform: uppercase; letter-spacing: 2px; }
        .section { margin-bottom: 25px; }
        .section-title { font-weight: bold; font-size: 16px; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 10px; color: #6366f1; }
        .details-table { width: 100%; border-collapse: collapse; }
        .details-table td { padding: 8px 0; border-bottom: 1px solid #f9f9f9; }
        .details-table .label { font-weight: bold; width: 150px; }
        .budget-box { background: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px; margin-top: 30px; }
        .budget-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .total { font-size: 20px; font-weight: bold; color: #6366f1; border-top: 2px solid #e2e8f0; padding-top: 10px; margin-top: 10px; text-align: right; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Event<span>AI</span> Manager</div>
        <div class="title">Pressupost d'Esdeveniment</div>
        <div style="font-size: 12px; color: #666;">Data: {{ now()->format('d/m/Y') }} | Ref: #APP-{{ $appointment->id }}</div>
    </div>

    <div class="section">
        <div class="section-title">Informació del Client</div>
        <table class="details-table">
            <tr><td class="label">Client:</td><td>{{ $appointment->name }}</td></tr>
            <tr><td class="label">Email:</td><td>{{ $appointment->email }}</td></tr>
            <tr><td class="label">Telèfon:</td><td>{{ $appointment->phone ?? 'N/A' }}</td></tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Detalls de l'Esdeveniment</div>
        <table class="details-table">
            <tr><td class="label">Tipus:</td><td>{{ $appointment->event_type }}</td></tr>
            <tr><td class="label">Data:</td><td>{{ $appointment->desired_date->format('d/m/Y') }}</td></tr>
            <tr><td class="label">Horari:</td><td>{{ $appointment->start_time }} - {{ $appointment->end_time }}</td></tr>
            <tr><td class="label">Assistents:</td><td>{{ $appointment->attendees }} persones</td></tr>
            <tr><td class="label">Ubicació:</td><td>{{ $appointment->location_name }}</td></tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Serveis Seleccionats</div>
        <table class="details-table">
            <tr><td class="label">Música:</td><td>{{ $appointment->wants_music ? ($appointment->music_type ?? 'Sí') : 'No' }}</td></tr>
            <tr><td class="label">Menú:</td><td>{{ $appointment->menu_type ?? 'Sense càtering' }}</td></tr>
            @if($appointment->dietary_requirements)
            <tr><td class="label">Requisits Diet.:</td><td>{{ $appointment->dietary_requirements }}</td></tr>
            @endif
        </table>
    </div>

    <div class="budget-box">
        <div class="section-title">Resum Econòmic</div>
        <table style="width: 100%;">
            <tr>
                <td style="padding: 10px 0;">Preu Base de l'Esdeveniment i Gestió</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($appointment->ai_estimated_budget, 2) }} €</td>
            </tr>
            <tr>
                <td style="padding: 10px 0;">IVA (21%)</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($appointment->ai_estimated_budget * 0.21, 2) }} €</td>
            </tr>
            <tr>
                <td class="total">Total Pressupost (IVA inclòs)</td>
                <td class="total">{{ number_format($appointment->ai_estimated_budget * 1.21, 2) }} €</td>
            </tr>
        </table>
    </div>

    <div class="section" style="margin-top: 30px;">
        <div class="section-title">Recomanacions de la nostra IA</div>
        <ul style="font-size: 13px;">
            @foreach($appointment->ai_recommendations as $rec)
                <li>{{ $rec }}</li>
            @endforeach
        </ul>
    </div>

    <div class="footer">
        Aquest pressupost ha estat generat automàticament per EventAI Manager.<br>
        Validesa del pressupost: 15 dies naturals.<br>
        Gràcies per confiar en nosaltres.
    </div>
</body>
</html>
