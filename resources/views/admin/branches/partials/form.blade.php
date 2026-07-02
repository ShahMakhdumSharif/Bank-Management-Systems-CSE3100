<div class="form-grid">
    <div class="form-field">
        <label for="name">Branch name</label>
        <input id="name" name="name" type="text" value="{{ old('name', $branch->name) }}" required>
        @error('name') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-field">
        <label for="branch_code">Branch code</label>
        <input id="branch_code" name="branch_code" type="text" value="{{ old('branch_code', $branch->branch_code) }}" required>
        @error('branch_code') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-field">
        <label for="city">City</label>
        <input id="city" name="city" type="text" value="{{ old('city', $branch->city) }}" required>
        @error('city') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-field">
        <label for="country_code">Country code</label>
        <input id="country_code" name="country_code" type="text" value="{{ old('country_code', $branch->country_code ?? 'BD') }}" required>
        @error('country_code') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-field form-wide">
        <label for="address">Address</label>
        <textarea id="address" name="address" required>{{ old('address', $branch->address) }}</textarea>
        @error('address') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <input name="is_active" type="hidden" value="0">
    <label class="checkbox-choice form-wide">
        <input name="is_active" type="checkbox" value="1" @checked(old('is_active', $branch->is_active))>
        Active branch
    </label>

    <fieldset class="checkbox-list">
        <legend>Assigned employees</legend>
        <div class="checkbox-grid">
            @forelse ($employees as $employee)
                <label class="checkbox-choice">
                    <input
                        name="employee_ids[]"
                        type="checkbox"
                        value="{{ $employee->id }}"
                        @checked(in_array($employee->id, old('employee_ids', $assignedEmployeeIds), false))
                    >
                    {{ $employee->name }} · {{ $employee->employee_code }}
                </label>
            @empty
                <p>No employees available yet.</p>
            @endforelse
        </div>
        @error('employee_ids') <p class="field-error">{{ $message }}</p> @enderror
    </fieldset>
</div>

<div class="form-actions">
    <button class="button" type="submit">{{ $submitLabel }}</button>
</div>
