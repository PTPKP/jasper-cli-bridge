package com.klinikcare.jaspercli;

import net.sf.jasperreports.engine.*;
import net.sf.jasperreports.json.data.JsonDataSource;

import java.io.*;
import java.sql.Connection;
import java.sql.DriverManager;
import java.util.HashMap;
import java.util.Map;

public class Main {
    public static void main(String[] args) {
        // Set headless mode for X11-less environments
        System.setProperty("java.awt.headless", "true");
        if (args.length < 3 || args.length > 4) {
            System.err.println("Usage: java -jar jasper-cli-bridge.jar <template.jrxml> <data.json|db> <output.pdf> [params.json]");
            System.exit(1);
        }
        String jrxmlPath = args[0];
        String dataArg = args[1];
        String pdfPath = args[2];
        Map<String, Object> params = new HashMap<>();
        if (args.length == 4) {
            String paramsPath = args[3];
            try (InputStream paramStream = new FileInputStream(paramsPath)) {
                params = new com.fasterxml.jackson.databind.ObjectMapper().readValue(paramStream, Map.class);
            } catch (Exception e) {
                System.err.println("Warning: Failed to load params from " + paramsPath + ": " + e.getMessage());
            }
        }

        try (InputStream jrxmlStream = new FileInputStream(jrxmlPath)) {
            System.err.println("Loading template: " + jrxmlPath);
            JasperReport jasperReport = JasperCompileManager.compileReport(jrxmlStream);
            System.err.println("Template compiled successfully");
            JasperPrint jasperPrint;
            if (dataArg.equalsIgnoreCase("db")) {
                // Load DB config from backend .env
                // Try multiple paths for .env file
                String envPath = null;
                String[] possiblePaths = {
                    "../../backend/.env",
                    System.getenv("APP_ENV_PATH"),
                    "/workspaces/KlinikCare/backend/.env"
                };
                File envFile = null;
                for (String path : possiblePaths) {
                    if (path != null) {
                        envFile = new File(path);
                        if (envFile.exists()) {
                            envPath = envFile.getAbsolutePath();
                            System.err.println("Found .env at: " + envPath);
                            break;
                        }
                    }
                }
                if (envPath == null) {
                    throw new RuntimeException("Could not find .env file at any of the expected locations");
                }
                Map<String, String> env = EnvLoader.loadEnv(envPath);
                String url = String.format("jdbc:postgresql://%s:%s/%s",
                        env.getOrDefault("DB_HOST", "localhost"),
                        env.getOrDefault("DB_PORT", "5432"),
                        env.getOrDefault("DB_DATABASE", "postgres"));
                String user = env.getOrDefault("DB_USERNAME", "postgres");
                String pass = env.getOrDefault("DB_PASSWORD", "");
                try (Connection conn = DriverManager.getConnection(url, user, pass)) {
                    jasperPrint = JasperFillManager.fillReport(jasperReport, params, conn);
                }
            } else {
                try (InputStream jsonStream = new FileInputStream(dataArg)) {
                    JsonDataSource jsonDataSource = new JsonDataSource(jsonStream);
                    jasperPrint = JasperFillManager.fillReport(jasperReport, params, jsonDataSource);
                }
            }
            JasperExportManager.exportReportToPdfFile(jasperPrint, pdfPath);
            System.out.println("PDF generated at: " + pdfPath);
        } catch (Exception e) {
            System.err.println("Error: " + e.getMessage());
            // Print root cause
            Throwable cause = e.getCause();
            while (cause != null) {
                System.err.println("Caused by: " + cause.getMessage());
                cause = cause.getCause();
            }
            e.printStackTrace();
            System.exit(2);
        }
    }
}
