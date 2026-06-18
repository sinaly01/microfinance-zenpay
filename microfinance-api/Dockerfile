# ============================================================
# ZEN-PAY Backend — Spring Boot 3.4.5 + Java 21
# Build multi-stage : maven (compile) → JRE (runtime, image légère)
# ============================================================

# ── Stage 1 : build (JDK 21) ────────────────────────────────
FROM maven:3.9-eclipse-temurin-21 AS builder

WORKDIR /app

# Cache des dépendances Maven (relance plus rapide si pom.xml inchangé)
COPY pom.xml .
RUN mvn dependency:go-offline -B

# Copie des sources et compilation
COPY src ./src
RUN mvn clean package -DskipTests -B

# ── Stage 2 : runtime (JRE 21) ──────────────────────────────
FROM eclipse-temurin:21-jre-alpine

WORKDIR /app

# Utilisateur non-root pour la sécurité
RUN addgroup -S spring && adduser -S spring -G spring
USER spring:spring

# Copie du jar depuis le builder
COPY --from=builder /app/target/*.jar app.jar

EXPOSE 8080

# Healthcheck : vérifie que l'API répond
HEALTHCHECK --interval=20s --timeout=5s --start-period=60s --retries=3 \
  CMD wget -qO- http://localhost:8080/actuator/health 2>/dev/null || \
      wget -qO- http://localhost:8080/v3/api-docs 2>/dev/null || exit 1

ENTRYPOINT ["java", "-jar", "/app/app.jar"]
