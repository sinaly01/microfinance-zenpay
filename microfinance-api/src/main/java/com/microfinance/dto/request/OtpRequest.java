package com.microfinance.dto.request;

import jakarta.validation.constraints.NotBlank;

public record OtpRequest(@NotBlank String email, @NotBlank String code) {
}
