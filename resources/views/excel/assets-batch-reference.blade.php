<div>
    <h2 style="font-weight: bold;">Referensi Kategori</h2>
    <table>
        <thead>
            <tr>
                <th style="font-weight:bold;">id</th>
                <th style="font-weight:bold;">name</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br />

    <h2 style="font-weight: bold;">Referensi Status</h2>
    <table>
        <thead>
            <tr>
                <th style="font-weight:bold;">value</th>
                <th style="font-weight:bold;">label</th>
            </tr>
        </thead>
        <tbody>
            @foreach($statuses as $status)
                <tr>
                    <td>{{ $status['value'] }}</td>
                    <td>{{ $status['label'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br />

    <h2 style="font-weight: bold;">Referensi Kondisi</h2>
    <table>
        <thead>
            <tr>
                <th style="font-weight:bold;">value</th>
                <th style="font-weight:bold;">label</th>
            </tr>
        </thead>
        <tbody>
            @foreach($conditions as $condition)
                <tr>
                    <td>{{ $condition['value'] }}</td>
                    <td>{{ $condition['label'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>