package com.klinikcare.jaspercli;

import java.io.*;
import java.util.HashMap;
import java.util.Map;

public class EnvLoader {
    public static Map<String, String> loadEnv(String envPath) throws IOException {
        Map<String, String> env = new HashMap<>();
        try (BufferedReader reader = new BufferedReader(new FileReader(envPath))) {
            String line;
            while ((line = reader.readLine()) != null) {
                line = line.trim();
                // Skip empty lines and comments
                if (line.isEmpty() || line.startsWith("#")) {
                    continue;
                }
                // Split on first = sign
                int eqIndex = line.indexOf('=');
                if (eqIndex <= 0) {
                    continue;
                }
                String key = line.substring(0, eqIndex).trim();
                String value = line.substring(eqIndex + 1).trim();
                
                // Remove inline comments - but be careful with quoted values
                if (!value.startsWith("\"") && !value.startsWith("'")) {
                    int commentIndex = value.indexOf('#');
                    if (commentIndex > 0) {
                        value = value.substring(0, commentIndex).trim();
                    }
                }
                env.put(key, value);
            }
        }
        return env;
    }
}

